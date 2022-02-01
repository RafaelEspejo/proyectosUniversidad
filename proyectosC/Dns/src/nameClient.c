

/*
*  C Implementation: nameClient
*
* Description: 
*
*
* Copyright: See COPYING file that comes with this distribution
*
*/

#include "nameClient.h"

/**
 * Function that sets the field addr->sin_addr.s_addr from a host name 
 * address.
 * @param addr struct where to set the address.
 * @param host the host name to be converted
 * @return -1 if there has been a problem during the conversion process.
 */
int setaddrbyname(struct sockaddr_in *addr, char *host)
{
  struct addrinfo hints, *res;
	int status;

  memset(&hints, 0, sizeof(struct addrinfo));
  hints.ai_family = AF_INET;
  hints.ai_socktype = SOCK_STREAM; 
 
  if ((status = getaddrinfo(host, NULL, &hints, &res)) != 0) {
    fprintf(stderr, "getaddrinfo: %s\n", gai_strerror(status));
    return -1;
  }
  
  addr->sin_addr.s_addr = ((struct sockaddr_in *)res->ai_addr)->sin_addr.s_addr;
  
  freeaddrinfo(res);
    
  return 0;  
}

/**
 * Function that gets the dns_file name and port options from the program 
 * execution.
 * @param argc the number of execution parameters
 * @param argv the execution parameters
 * @param reference parameter to set the host name.
 * @param reference parameter to set the port. If no port is specified 
 * the DEFAULT_PORT is returned.
 */
int getProgramOptions(int argc, char* argv[], char *host, int *_port)
{
  int param;
  *_port = DEFAULT_PORT;

  // We process the application execution parameters.
	while((param = getopt(argc, argv, "h:p:")) != -1){
		switch((char) param){		
			case 'h':
				strcpy(host, optarg);				
				break;
			case 'p':
				// Donat que hem inicialitzat amb valor DEFAULT_PORT (veure common.h) 
				// la variable port, aquest codi nomes canvia el valor de port en cas
				// que haguem especificat un port diferent amb la opcio -p
				*_port = atoi(optarg);
				break;				
			default:
				printf("Parametre %c desconegut\n\n", (char) param);
				return -1;
		}
	}
	
	return 0;
}

/**
 * Shows the menu options. 
 */
void printa_menu()
{
		// Mostrem un menu perque l'usuari pugui triar quina opcio fer

		printf("\nAplicatiu per la gestió d'un DNS Server\n");
		printf("  0. Hola mon!\n");
		printf("  1. Llistat dominis\n");
		printf("  2. Consulta domini\n");
		printf("  3. Alta Ip\n");
		printf("  4. Alta Ips\n");
		printf("  5. Modificacio Ip\n");
		printf("  6. Baixa Ip\n");
		printf("  7. Baixa Domini\n");
		printf("  8. Sortir\n\n");
		printf("Escolliu una opcio: ");
}

void process_Hello(int s){
	unsigned short code;
	char buffer[MAX_BUFF_SIZE];
	sendOpCodeMSG(s, MSG_HELLO_RQ);
	memset(buffer,'\0',sizeof(buffer));
	if (recv(s,buffer,sizeof(buffer),0)==ERROR){
		perror("Error al recibir el mensaje");
		return (EXIT_FAILURE);
	}
	code=ldshort(buffer);
	printf("Missatge rebut: %s. Codi d'operacio: %i\n",buffer+sizeof(short),code);
}
void process_Domain_RQ(int s){
	char buffer[MAX_BUFF_SIZE];
	unsigned short code;
	int size=0;
	char domini[100];
	printf("Introdueixi el domini a buscar:\n");
	setbuf( stdin, NULL );//limpia el buffer del teclado
	fgets(domini,sizeof(domini),stdin);
	size=strlen(domini);
	domini[size-1]='\0';
	memset(buffer,'\0',sizeof(buffer));
	stshort(MSG_DOMAIN_RQ,buffer);
	strcpy(buffer+2,domini);
	size=strlen(domini)+2;
	send(s,buffer,size,0);
	memset(buffer, '\0', MAX_BUFF_SIZE);
	if ((size=recv(s,buffer,sizeof(buffer),0))==ERROR){
	  		perror("Error al recibir el mensaje");
	  		return (EXIT_FAILURE);
	  	}
	  code=ldshort(buffer);

	  printf("Codi d'operació: %i\n",code);
	  if (code == MSG_IP_LIST){
		  int i=2;
		  printf("Ips del domini %s:",domini);
		  while(i<size){
			  printf(" %s ",inet_ntoa(ldaddr(buffer+i)));
		  	i+=sizeof(struct in_addr);
		  }

	  }else if(code==MSG_OP_ERR){
		  printf("Codi d'error %i: ",ldshort(buffer+sizeof(unsigned short)));
		  printf("%s\n",nodomain);
	  }
}

void process_AddDomain(int s){
	char buffer[MAX_BUFF_SIZE];
	char Ips[32];
	struct in_addr address;
	unsigned short code;
	int offset=sizeof(unsigned short);
	char domini[100];
	memset(buffer,'\0',sizeof(buffer));
	stshort(MSG_ADD_DOMAIN,buffer);
	printf("Introdueixi un domini existent o un nou domini:\n");
	setbuf( stdin, NULL );//limpia el buffer del teclado
	fgets(domini,sizeof(domini),stdin);
	int size=strlen(domini)-1;
	domini[size]='\0';
	offset+=(strlen(domini)+1);
	strcpy(buffer+sizeof(unsigned short),domini);
	printf("Introdueixi una ip:\n");
	setbuf( stdin, NULL );//limpia el buffer del teclado
	fgets(Ips,sizeof(Ips),stdin);
	inet_aton(Ips, &address);
	staddr(address, buffer+offset);
	offset+=sizeof(struct in_addr);
	send(s,buffer,offset,0);
	memset(buffer, '\0', MAX_BUFF_SIZE);
	if ((recv(s,buffer,sizeof(buffer),0))==ERROR){
		  perror("Error al recibir el mensaje");
		  return (EXIT_FAILURE);
	}
	code=ldshort(buffer);
	printf("Codi d'operació: %i ",code);
	if (code==MSG_OP_OK){
		printf("%s\n",ok);
	}else{
		printf("%s\n",nook);
	}
}
void process_AddDomain_IPS(int s){
		char buffer[MAX_BUFF_SIZE];
		char Ips[100];
		struct in_addr address;
		unsigned short code;
		int offset=sizeof(unsigned short);
		char domini[100];
		memset(buffer,'\0',sizeof(buffer));
		stshort(MSG_ADD_DOMAIN,buffer);
		printf("Introdueixi un domini existent o un nou domini:\n");
		setbuf( stdin, NULL );//limpia el buffer del teclado
		fgets(domini,sizeof(domini),stdin);
		int size=strlen(domini)-1;
		domini[size]='\0';
		offset+=(strlen(domini)+1);
		strcpy(buffer+sizeof(unsigned short),domini);
		printf("Introdueixi les ips del domini posant espais entre una ip i una altre:\n");
		setbuf(stdin, NULL );//limpia el buffer del teclado
		memset(Ips,'\0',sizeof(Ips));
		fgets(Ips,sizeof(Ips),stdin);
		int off=0;
		do{
			if(off==0){
				inet_aton(Ips+off, &address);
				staddr(address, buffer+offset);
				offset+=sizeof(struct in_addr);
			}
			if(Ips[off]!=' '){
				off++;
			}else{
				off++;
				inet_aton(Ips+off, &address);
				staddr(address, buffer+offset);
				offset+=sizeof(struct in_addr);
			}
		}while(Ips[off]!='\n');
		send(s,buffer,offset,0);
		memset(buffer, '\0', MAX_BUFF_SIZE);
		if ((recv(s,buffer,sizeof(buffer),0))==ERROR){
				  perror("Error al recibir el mensaje");
				  return (EXIT_FAILURE);
		}
		code=ldshort(buffer);
		printf("Codi d'operació: %i ",code);
		if (code==MSG_OP_OK){
			printf("%s\n",ok);
		}else{
			printf("%s\n",nook);
		}
}
void process_change_domain(int s){
	char buffer[MAX_BUFF_SIZE];
	char Ips[100];
	struct in_addr address;
	unsigned short code;
	int offset=sizeof(unsigned short);
	char domini[100];
	memset(buffer,'\0',sizeof(buffer));
	stshort(MSG_CHANGE_DOMAIN,buffer);
	printf("Introdueixi el domini en el qual vol modificar la ip:\n");
	setbuf( stdin, NULL );//limpia el buffer del teclado
	fgets(domini,sizeof(domini),stdin);
	int size=strlen(domini)-1;
	domini[size]='\0';
	offset+=(strlen(domini)+1);
	strcpy(buffer+sizeof(unsigned short),domini);
	printf("Introdueixi la antiga IP i la nova separades per un espai:\n");
	setbuf(stdin, NULL );//limpia el buffer del teclado
	memset(Ips,'\0',sizeof(Ips));
	fgets(Ips,sizeof(Ips),stdin);
	int off=0;
	do{
		if(off==0){
			inet_aton(Ips+off, &address);
			staddr(address, buffer+offset);
			offset+=sizeof(struct in_addr);
		}
		if(Ips[off]!=' '){
			off++;
		}else{
			off++;
			inet_aton(Ips+off, &address);
			staddr(address, buffer+offset);
			offset+=sizeof(struct in_addr);
		}
	}while(Ips[off]!='\n');
	send(s,buffer,offset,0);
	memset(buffer, '\0', MAX_BUFF_SIZE);
	if ((recv(s,buffer,sizeof(buffer),0))==ERROR){
			perror("Error al recibir el mensaje");
			return (EXIT_FAILURE);
		}
	code=ldshort(buffer);
	printf("Codi d'operació: %i ",code);
	if (code==MSG_OP_OK){
		printf("%s\n",ok);
	}else if(code==MSG_OP_ERR){
		printf("%s ",nook);
		if(ldshort(buffer+sizeof(unsigned short))==ERR_1){
			printf("%s\n",noip);
		}else if(ldshort(buffer+sizeof(unsigned short))==ERR_2){
			printf("%s\n",nodomain);
		}
	}
}
void process_finish(int s){
	sendOpCodeMSG(s, MSG_FINISH);
}
/**
 * Function that sends a list request receives the list and displays it.
 * @param s The communications socket. 
 */
void process_list_operation(int s)
{
  unsigned short code;
  char buffer[DNS_TABLE_MAX_SIZE];
  int msg_size;
  sendOpCodeMSG(s, MSG_LIST_RQ);
  memset(buffer, '\0', sizeof(buffer));
  if ((msg_size=recv(s,buffer,sizeof(buffer),0))==ERROR){
  		perror("Error al recibir el mensaje");
  		return (EXIT_FAILURE);
  	}
  code=ldshort(buffer);
  printf("Codi d'operació: %i\n",code);
  printDNSTableFromAnArrayOfBytes(buffer+sizeof(short), msg_size-sizeof(short));
}

/** 
 * Function that process the menu option set by the user by calling 
 * the function related to the menu option.
 * @param s The communications socket
 * @param option the menu option specified by the user.
 */
void process_menu_option(int s, int option)
{
  switch(option){
    // Opció HELLO
    case MENU_OP_HELLO:
    	process_Hello(s);
      break;
    case MENU_OP_LIST:
      process_list_operation(s);
      break;
    case MENU_OP_DOMAIN_RQ:
      process_Domain_RQ(s);
      break;
    case MENU_OP_ADD_DOMAIN_IP:
    	process_AddDomain(s);
        break;
    case MENU_OP_ADD_DOMAIN_IPS:
    	process_AddDomain_IPS(s);
    	break;
	case MENU_OP_CHANGE:
		process_change_domain(s);
		break;
    case MENU_OP_FINISH:
      process_finish(s);
      break;
                
    default:
          printf("Invalid menu option\n");
  		}
}

int main(int argc, char *argv[])
{
	int port; // variable per al port inicialitzada al valor DEFAULT_PORT (veure common.h)
	char host[MAX_HOST_SIZE]; // variable per copiar el nom del host des de l'optarg
	int option = 0; // variable de control del menu d'opcions
	int ctrl_options;
	int s;
	struct sockaddr_in server;

  ctrl_options = getProgramOptions(argc, argv, host, &port);

	// Comprovem que s'hagi introduit un host. En cas contrari, terminem l'execucio de
	// l'aplicatiu	
	if(ctrl_options<0){
		perror("No s'ha especificat el nom del servidor\n\n");
		return -1;
	}

 //TODO: setting up the socket for communication
	if((s=socket(PF_INET, SOCK_STREAM, IPPROTO_TCP))==ERROR){
		perror("Error al crear el socket del client");
		return EXIT_FAILURE;
	}
	server.sin_family=AF_INET;
	server.sin_port=htons(port);
	setaddrbyname(&server,host);
	if (connect(s,(struct sockaddr *)&server,sizeof(struct sockaddr))==ERROR){
		perror("Error al conectar-se al servidor");
		return EXIT_FAILURE;
	}
  do{
      printa_menu();
		  // getting the user input.
		  scanf("%d",&option);
		  printf("\n\n"); 
		  process_menu_option(s, option);

	  }while(option != MENU_OP_FINISH); //end while(opcio)

  close(s);

  return 0;
}

