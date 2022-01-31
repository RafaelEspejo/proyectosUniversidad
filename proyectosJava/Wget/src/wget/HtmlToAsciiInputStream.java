package wget;

import java.io.FilterInputStream;
import java.io.IOException;
import java.io.InputStream;

public class HtmlToAsciiInputStream extends FilterInputStream{
	/**
	 * Constructor de la classe rep un InputStream.
	 * @param input
	 * Contingut descarregat de una url
	 */
	public HtmlToAsciiInputStream(InputStream input) {
		super(input);
	}
	/**
	 * Sobrecarrega de la funció read de la classe FilterInputStream que filtra els tags html.
	 * @public int read()
	 * @exception IOException
	 * Mostra un missatge d'error
	 */
	public int read() {
		int b=-1;
		try {
			while ((b=in.read())!=-1) {
				if((char)b=='<') {
					while((char)(b=in.read())!='>');
				}else {
					return b;
				}
			}
			}catch(IOException e) {
				System.out.println(e.getMessage());
			}
		return b;
	}
}
