package mapreduce;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.Date;

public class MapReduce extends Thread {

	private String[] archivo = null; //variable que contiene los nombres de los archivos
	private String encoding = null; //variable que contiene la codificacion de los caracteres
	private int n_threads = 0; //variable que contiene el numeros de threads Mapp a jecutar
	private Reduce reduce = null; //variable que contiene la referencia a Reduce
	private boolean guardarSalida; //variable que contiene si queremos o no guardar el archivo

	/**
	 * constructor del programa principal, por parametro recibe todo lo que le pasamos al programa
	 * 
	 */
	public MapReduce(final String[] p) { 
		if (p.length >= 7) { //nos aseguramos de que hayan los parametros necesarios para la ejecucion del programa
			if ((p[0].equals("-t") && p[2].equals("-o") && p[4].equals("-e") && p[6].equals("-f"))
					&& (!p[1].isEmpty() && !p[3].isEmpty() && !p[5].isEmpty())) { //nos aseguramos que el formato del programa es el correcto
				try {
					int t = Integer.parseInt(p[1]); //transformacion a interger del parametro que contiene la cantidad de threads a ejecutar
					if (t >= 2) { //la cantidad de threads debe ser 2 o mayor
						this.n_threads = t; //inicialiamos la variable que contiene el total de threads deseados
					} else {
						this.n_threads = 2; //si no se pasa el parametro en blanco inicializamos con 2 threads minimos
					}
				} catch (NumberFormatException e) {
					this.n_threads = 2; //si se pasan caracteres no numericos inicializamos con 2 threads minimos
				}
				if (p[3].contains("y")) { //comprobacion de si queremos guardar el archivo o no
					this.guardarSalida = true;
				} else {
					this.guardarSalida = false;
				}

				this.encoding = p[5]; //se le pasa el tipo de codificacion de los caracteres			
				this.archivo = new String[p.length-7]; //inicializacion de la variable que contiene el nombre de los archivos pasados por parametro
				for (int j = 7, i = 0; j < p.length; j++, i++) {
					this.archivo[i] = p[j];
				}
				this.reduce = Reduce.getInstance(this); //inicializamos una unica instancia de la clase Reduce donde se le pasa la instancia de esta clase
			}
		}
	}
	/**
	 * Funcion donde se ejecuta el thread master del programa.
	 * En el se lee los archivos pasados por parametro y dependiendo 
	 * de los threads que se le pase por parametro dividira la x lineas
	 * del archivo entre x threads Mapp que se desee ejecutar el 
	 * programa.
	 * Inicica el thread master Reduce.
	 */
	public void run() {
		try {
			this.reduce.start(); //inicio del thread master Reduce
			Date start = null; //variable que guardara el inicio de la ejecucion de cada archivo
			String resultado = "";
			for (int p = 0; p < archivo.length; p++) { //ejecucion de los x archivos pasados por parametro
				start = new Date();
				BufferedReader bufferreader = new BufferedReader(
						new InputStreamReader(new FileInputStream(archivo[p]), encoding)); //buffer para cargar las lineas del archivo codificadas
				
				ArrayList<String> linea = new ArrayList<String>(); //lista donde estaran todas las lineas de un archivo
				System.out.println(this.archivo[p] + ":");
				try {
					String l = "";
					while ((l = bufferreader.readLine()) != null) { //lectura del archivo linea por linea y cada linea se guardara en un arraylist 
						try {
							if (!l.equals("")) {
								linea.add(l);
							}
						} catch (OutOfMemoryError e) {
							e.printStackTrace();
						}
					}
					int n_lineas = (int) Math.round((double) linea.size() / this.n_threads); //eleccion de cuantas lineas ejecutara un thread Mapp
					int posicion = 0;
					for (int i = 0; i < this.n_threads; i++) { //aqui se le pasara a cada thrad Mapp las lineas con las que trabajara
						ArrayList<String> t = new ArrayList<String>(); //lista que contendra una cantidad x de lineas para pasarselas a un thread Mapp
						for (int j = 0; j < n_lineas; j++) { //seleccion de cuantas lineas ejecutara la clase mapp
							if (posicion < linea.size()) {
								t.add(linea.get(posicion));
								posicion++;
							}
						}
						new Mapp(t).start(); //inicializacion de un thread mapp pasandole una parte de los datos
					}
					linea.clear(); //eliminacion de los datos del archivo en general para liberar memoria
					bufferreader.close(); //cerramos el buffer de lectura
					synchronized (this) { //suspension del thread de la clase principal a la espera que la clase reduce termine su trabajo
						this.wait();      //funcion propia de la clase thread para poner en suspension a un thread hasta que reciba un notify para despertarse
					} 				
					resultado = reduce.getResultado(); //se pide al thread master Reduce el resultado
					System.out.println(resultado);
					if (this.guardarSalida) { //si hemos pasado por parametro que se guarde el resultado final en un archivo llamara a la funcion para guardar el resultado final de todo el programa
						this.guardarArchivoSalida(archivo[p], resultado, start);
					}
					resultado = "";

				} catch (IOException e) {
					e.printStackTrace();
				} catch (InterruptedException e) {
					e.printStackTrace();
				}
			}
			reduce.setStopThread(false); //cuando ya hemos terminado de ejecutar todos los archivos paramos el thread master Reduce
		} catch (FileNotFoundException e) {
			e.printStackTrace();
		} catch (UnsupportedEncodingException e1) {
			e1.printStackTrace();
		}
	}
	
	/**
	 * Funcion que guarda el resultado en un archivo 
	 * 
	 */
	private void guardarArchivoSalida(final String a, final String r, Date t) {
		try {
			BufferedWriter bw = new BufferedWriter(new OutputStreamWriter(new FileOutputStream("Salida" + "_" + a.split("\\.")[0] + ".txt"), encoding));
			try {
				String datos = "\nResultado del archivo " + a + ":\n" + r
						+ "\nDatos de ejecucion del archivo " + a + "\n" + "Ejecucion con "
						+ this.n_threads + " threads\n" + "Tiempo de ejecución: "
						+ ((long) (new Date().getTime()) - t.getTime()) + " ms\n"; // guardamos el resultado final para guardarlo en un archivo
				bw.write(datos);
				bw.close();
			} catch (IOException e) {
				e.printStackTrace();
			}

		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		} catch (FileNotFoundException e) {
			e.printStackTrace();
		}
	}

	/**
	 * Main principal del programa y inicializacion de thread master del programa
	 * 
	 */
	public static void main(final String[] args) {
		new MapReduce(args).start();
	}
}