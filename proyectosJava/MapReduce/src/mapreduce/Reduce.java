package mapreduce;

import java.util.EmptyStackException;
import java.util.Map;
import java.util.Map.Entry;
import java.util.Stack;

public class Reduce extends Thread {
	//pila publica vista por todos los threads Mapp y threads independientes Reduce y que contiene los datos parciales o finales de la ejecucion
	public static volatile Stack<Map<String, Integer>> COLA = new Stack<Map<String, Integer>>(); 
	private boolean ejecutar = true; //variable que sirve para tener en ejecucion el thread master Reduce
	private static volatile Reduce instance = null; //variable que guarda la unica instancia que se puede hacer a esta clase
	private static int controlThreads = 0; //variable para saber si hay threads independientes ejecutandose o no
	private MapReduce mapreduce = null; //instancia de la clase principal para asi poder despertar al thread que dara el resultado por pantalla
	
	/**
	 * Constructor de la clase Reduce
	 */
	public Reduce(MapReduce m) {
		this.mapreduce = m;
	}
	/**
	 * Funcion singleton de la clase Reduce donde se llama se inicializa una unica vez esta clase
	 * 
	 */
	public static Reduce getInstance(MapReduce m) {
		if (instance == null) {
			synchronized (Reduce.class) {
				if (instance == null) {
					instance = new Reduce(m);
				}
			}
		}
		return instance;
	}
	/**
	 * Funcion que inicializa el thread master de Reduce
	 */
	public void run() {
		while (ejecutar) {
			try {
				if (COLA.size() >= 2 && !COLA.empty()) { //si la pila no esta vacia y hay 2 o mas resultados, crea un thread independiente que suma dos diccionarios y los vuelve añadir a la pila
					(new Thread(new Runnable() { //thread independiente que hace shuffle entre diccionarios
						public void run() {
							controlThreads++; //incremento de la variable de control de threads para sumar los diccionarios
							Map<String, Integer> map1 = null;
							Map<String, Integer> map2 = null;
							try {
								map1 = COLA.pop(); //extraccion de la pila de un diccionario
								map2 = COLA.pop(); //extraccion de la pila de otro diccionario
								for (Entry<String, Integer> entrada : map2.entrySet()) { //suma de los diccionarios
									if (map1.containsKey(entrada.getKey())) {
										map1.put(entrada.getKey(), (map1.get(entrada.getKey()) + entrada.getValue()));
									} else {
										map1.put(entrada.getKey(), entrada.getValue());
									}
								}
								COLA.push(map1); //devuelve el resultado a la pila
								controlThreads--; //cuando acaba su funcion resta uno para indicar que este thread ha acabado y no esta activo
							} catch (EmptyStackException e1) { 
								//si cuando entro a la pila habia dos diccionarios o mas pero al extraerlos otro thread independiente extrae un diccionarios 
								//y la pila se queda vacia devuelve a la pila el diccionario para no perder datos
								if (map1 != null) {
									COLA.push(map1);
								}
								if (map2 != null) {
									COLA.push(map2);
								}
								controlThreads--;//cuando acaba su funcion resta uno para indicar que este thread ha acabado y no esta activo
							}
						}
					})).start();
				} else if (COLA.size() == 1 && controlThreads == 0 && Mapp.controlTHMap == 0) { //si en la pila hay un diccionario y no hay ni threads independientes ni threads mapp ejecutandose, considera que es el resultado final y comunica al thread master que puede recoger los datos
					synchronized (this.mapreduce) { //cuando reduce ha acabado finalmente usa notify para despertar al thread de la clase principal de su suspension
						this.mapreduce.notify();
					}
				}
				sleep(4);
			} catch (Exception e) {
				e.printStackTrace();
			}
		}
	}

	/**
	 * Funcion que devuelve el resultado del archivo 
	 * 
	 */
	public String getResultado() {
		String resultado = "";
		Map<String, Integer> map = COLA.pop();
		for (Entry<String, Integer> entrada : map.entrySet()) {
			resultado = resultado + entrada.getKey() + " : " + entrada.getValue() + "\n";
		}
		return resultado;
	}
	
	/**
	 * Funcion que finaliza el bucle de esta clase activo
	 * 
	 */
	public void setStopThread(final boolean b) {
		ejecutar = b;
	}

}