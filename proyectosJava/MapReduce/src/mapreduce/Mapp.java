package mapreduce;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

public class Mapp extends Thread {

	private ArrayList<String> linea = null; //variable que contiene las lineas parciales del archivo
	private Map<String, Integer> palabras = new HashMap<String, Integer>(); //diccionario donde se guardaran todas las palabras que contenga la lista de lineas
	public static int controlTHMap = 0; //variable de control para saber si hay algun thread mapp en ejecucion, usada principalmente en la clase reduce
	
	/**
	 * Constructor de la clase Mapp
	 * 
	 */
	public Mapp(final ArrayList<String> l) {
		this.linea = l;
		controlTHMap++; //incremento de la variable de control
	}
	/**
	 * Funcion para iniciar un thread Mapp, en ella se llamara a la funcion crearMapa que creara el diccionario de palabras para este thread
	 * al acabar le enviara el diccionario a una pila publica que tiene la clase Reduce para juntar el resultado de los diferentes threads Mapp
	 * y dar el resultado final
	 */
	public void run() { 

	  this.crearMapa();
	  Reduce.COLA.push(palabras);
   	  controlTHMap--; //decremento de la variable de control
	}

	/**
	 * Funcion que crea el diccionario de palabras de una parte del archivo.
	 * Lo primero que hace es separar cada palabra de cada linea y luego le añade las veces que se repite esa palabra,
	 * como se perderian palabras repetidas en una misma linea la funcion las cuentea para no perderlas ya que el diccionario
	 * solo acepta una unica clave que es una palabra.
	 * Tambien pasa a minusculas todas las palabras y limpia todos los signos de puntuacion y numeros.
	 */
	private void crearMapa() {
		String regex = "[\\p{Punct}\\p{Digit}]+";
		String sub = "";
		for (String li : this.linea) {
			String[] l = li.replaceAll(regex, sub).split(" ");
			for (String palabra : l) {
				if (palabra.equals("") == false) {
					palabra = palabra.toLowerCase();
					if (palabras.containsKey(palabra) && !palabra.isEmpty()) {
						palabras.put(palabra, (palabras.get(palabra) + 1));
					} else {
						palabras.put(palabra, 1);
					}
				}
			}
		}
		this.linea.clear();
	}
}