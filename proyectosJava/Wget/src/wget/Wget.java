package wget;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.FileReader;

public class Wget {
	private String parent="";
	private String filters;
	/**
	 * Constructor de la classe Wget que rep els parametres passats al programa
	 * @param parametros
	 * Conté el nom del fitxer on estan les urls i els filtres a fer servir en el programa
	 * 
	 */
	public Wget(String[] parametros) {
		try {
		switch (parametros.length) {
		case 3: 
			this.parent=parametros[1];
			this.filters= parametros[2];
		break;
		case 4:
			this.parent=parametros[1];
			this.filters= parametros[2]+parametros[3];
		break;
		case 5:
			this.parent=parametros[1];
			this.filters= parametros[2]+parametros[3]+parametros[4];
		break;
		default:
			this.parent=parametros[1];
		break;
		}
		}catch(ArrayIndexOutOfBoundsException e) {
			System.out.println("No has passat cap fitxer");
		}
		
	}
	/**
	 * LLegeix el fitxer de les urls, guarda el seu contingut en un buffer i inicia l'execució d'un thread per url
	 * cridant a la classe {@link URL_download}.
	 * @public void leerFichero()
	 * @exception IOException
	 * Excepció quan no troba el fitxer
	 */
	public void leerFichero() {
		try {
			BufferedReader urls=new BufferedReader(new FileReader(parent));
			String url;
			if(filters== null || (filters!=null && (filters.contains("-a") || filters.contains("-z") || filters.contains("-gz") || filters.contains("-z-gz") || filters.contains("-a-z-gz")))) {
			while((url=urls.readLine())!=null) {
				URL_download uri=new URL_download(url, filters);
				uri.start();
			}
			urls.close();
			}else {
				urls.close();
				System.out.println("Parametre passat no existeix");
			}
			}catch(IOException e) {
				System.out.println("No s'ha trobat el fitxer");
			}
	}
	/**
	 * Main del programa Wget
	 * @param args
	 * Conté els parametres passats al iniciar el programa
	 */
	public static void main(String[] args){
		new Wget(args).leerFichero();
	}
}
