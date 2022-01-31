package wget;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.UnknownHostException;
import java.util.zip.GZIPOutputStream;
import java.util.zip.ZipEntry;
import java.util.zip.ZipOutputStream;


public class URL_download extends Thread{
	private String urlparent; //conte la url 
	private String filters; //conte els filtres passats per paràmetre
	private InputStream buffer; //variable on es guardara el contingut de la web 
	private String file=""; //conte el nom del fitxer resultant
	private String extension=""; //conte l'extensió final del fitxer si els filtres estan actius
	private boolean image=false; //serveix per saber si en la url hi ha una imatge
	private static int contador=0; //variable compartida amb tots el threads i que s'afegeix al nom del fitxer
	
	/**
	 * Constructor de la classe rep una url i els filtres que s'utilitzaren.
	 * @public URL_download(String urlparent, String filters
	 * @param urlparent
	 * Paràmetre que conté la url
	 * @param filters
	 * Paràmetre que conté els filtres  
	 */
	public URL_download(String urlparent, String filters) {
		this.urlparent=urlparent;
		this.filters=filters;
	}
	/**
	 * Metode que inicia l'execució d'un thread, es conecta a internet, es descarrega una web. 
	 * Si el paràmetre -a esta actiu crida a {@link HtmlToAsciiInputStream} i filtra el que s'ha descarregat de la web. 
	 * Si la web conté una imatge no filtre res del que s'ha descarregat, això esta controlotat per la variable boleana image.
	 * Incrementa la variable contador (variable utilitzada per generar el nom dels fitxers) 
	 * i crida a la funció guardarFitxer() per guarda el que s'ha descarregat.
	 * 
	 * @see nombreFitxer(URL url)
	 * @see extensionPorParametro()
	 * @see guardarFitxer()
	 * @public void run()
	 * @exception UnknownHostException
	 * Excepció que mostra un missatge quan no hi ha conexió o la url es desconeguda
	 * @exception MalformedURLException
	 * Excepció que mostra un missatge 
	 * @exception IOException
	 * Excepció que mostra el codi de error donat pel servidor web 
	 * 
	 */
	public void run() {
		int codierror=0;
		 try {		 	
	            URL url = new URL(urlparent);
	            HttpURLConnection conexion = (HttpURLConnection) url.openConnection();
	            codierror=conexion.getResponseCode();//Si el servidor envia un codi error el recollim aqui
	            nombreFitxer(url);//creacio del nom del fitxer
	            if (filters!= null) extensionPorParametro();//creacio de la extensio final del fitxer si ha actiu algun filtre
	            try {
	                conexion.connect();
	                System.out.println("Iniciant descarregar de la web: "+urlparent);
	                
	                if (filters != null && filters.contains("-a") && image==false) {
	                	buffer= new HtmlToAsciiInputStream(conexion.getInputStream());//filtra el que descarrega de la web sempre que no sigui una imatge
	                }/*else if(filters != null && filters.contains("-a") && image==true) {
	                	buffer=conexion.getInputStream();//descarrega el contingut de la web que es una imatge
	                }*/else {
	                	buffer=conexion.getInputStream();//descarrega el contingut de la web sense filtrar-ho
	                }
	                
	                contador+=1;
	                guardarFitxer();//crida a la funcio per guarda el contingut de la web
	                conexion.disconnect();//tanquem la conexio a la web quan termina de descarregar el contingut
	            } catch (UnknownHostException o) {
	                conexion.disconnect();
	                System.out.println("No hay conexion");//excepcio que salta quan no hi ha conexio
	            }
	        } catch (MalformedURLException e) {
	        	System.out.println(e.getMessage());
	        }catch (IOException ex) {
	        	System.out.println("Error "+codierror+": "+ex.getMessage());//excepcio que salta quan el servidor envia un codi d'error 
	        }
		
	}
	/**
	 * Metode que crea el fitxer resultant amb el contingut de la web. 
	 * Si els filtres -z o -gz o tots dos estan actius 
	 * comprimeix el contingut de la web en un fitxer Zip, GZip o Zip GZip.
	 * @public void guardFitxer()
	 * @exception IOException
	 * Si no es pot guardar el fitxer crea una excepció
	 */
	public void guardarFitxer() {
		
		File arxiu_sortida=null;
		int lectura=0;
		try{
			OutputStream sortida=null;
			arxiu_sortida= new File(file+extension);
			sortida=new FileOutputStream(arxiu_sortida);
			if (filters!=null) {
				if (filters.contains("-gz")) {
					sortida = new GZIPOutputStream(sortida);//comprimeix el contingut de la web en GZip
				}if(filters.contains("-z")) {
					sortida = new ZipOutputStream(sortida);
					((ZipOutputStream) sortida).putNextEntry(new ZipEntry(file));//comprimeix el contingut de la web en Zip
				}			
			}
			if (!arxiu_sortida.exists()) {
				arxiu_sortida.createNewFile();//crea el fitxer si no existeix
			}
			System.out.println("Descarregant contingut de la web: "+urlparent);
			while ((lectura = buffer.read())!= -1) {
				sortida.write(lectura);//escritura en el fitxer el contingut de la web
				//System.out.println((char)lectura);
			}
			sortida.close();//tanca el OutputStream
			buffer.close();//tanca el InputStream
			System.out.println("Descarrega de la web "+urlparent+" guardat al fitxer "+arxiu_sortida);
		}catch (IOException e) {
			System.out.println("Error al guardar el fitxer"+e.getMessage());
		}
	}
	/**
	 * Metode que genera el nom del fitxer amb la seva extensió (.html,.php,.png.jpg,....)
	 * @public void nombreFitxer(Url url)
	 * @param url
	 * Variable que conté url de la web
	 */
	public void nombreFitxer(URL url) {
		if (url.getPath().equals("/index.html")){//si el contingut en el path /index.html li afegim el contador
			String aux=url.getPath().split("/")[1].split("\\.")[0];
			file=aux+contador+".html";
		}else if(url.getPath().isEmpty()) {//si no hi ha cap path, creem el nom index.html mes el contador
			file="index"+contador+".html";
		}else if(url.getPath().equals("/")){
			file="index"+contador+".html";
		}else {//si el contingut en el path es diferent a index.html 
			String[] auxs=url.getPath().split("/");
			String auxiliarFile=auxs[auxs.length-1];
			if (!auxiliarFile.contains(".")) {//si el contingut al final del path es buit, creem el nom index.html mes el contador
				file="index"+contador+".html";
			}else {//si el contingut al final del path no es buit creem el nom mes el contador
				auxs=auxiliarFile.split("\\.");
				file=auxs[0]+contador+"."+auxs[1];
				if (auxs[1].equals("png")|| auxs[1].equals("jpg")|| auxs[1].equals("gif")||auxs[1].equals("bmp")||auxs[1].equals("tif")) image=true; //si el contingut del path es una imatge activa el flag image
			}	
		}
			//contemplar el caso de http://www.google.es/ pq peta con esa direccion por la barra ya que no hay caso que lo filtre
	}
	/**
	 * Metode que genera la extensió del fitxer resultant si hi ha actius algun filtre (.asc,.zip.gz).
	 * @public void extensionPorParametro()
	 */
	public void extensionPorParametro() {
		if(image==false) {//si no hi ha cap imatge
		if (filters.equals("-a-z-gz")) {extension=".zip.gz"; file=file+".asc";}
		else if (filters.equals("-z-gz")) extension=".zip.gz";
		else if (filters.equals("-a-gz")) {extension=".gz"; file=file+".asc";}
		else if (filters.equals("-a-z")) {extension=".zip"; file=file+".asc";}
		else if(filters.equals("-z")) extension=".zip";
		else if(filters.equals("-gz")) extension=".gz";
		else if(filters.equals("-a")) extension=".asc";
		else extension="";
		}else if(image==true) {//si hi ha alguna imatge
			if (filters.contains("-z-gz")) extension=".zip.gz";
			else if(filters.contains("-z")) extension=".zip";
			else if(filters.contains("-gz")) extension=".gz";
			else extension="";
		}
	}
}
