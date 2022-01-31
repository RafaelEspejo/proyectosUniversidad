
/*
 *  C Implementation: nameServer
 *
 * Description:
 *
 *
 * Copyright: See COPYING file that comes with this distribution
 *
 */

#include "nameServer.h"


/* Reads a line ended with \n from the file pointer.  */
/* Return: a line ended not with an EOL but with a 0 or NULL if the end of the
file is reached */
char *readLine(FILE *file, char *line, int sizeOfLine)
{

	int line_length;

	if (fgets(line, sizeOfLine, file) != NULL)
	{
		line_length = strlen(line)-1;
		line[line_length] = 0;
	}
	else
	{
		line = NULL;
	}

	return line;
}


/**
 * Creates a DNSEntry variable from the content of a file line and links it 
 * to the DNSTable. 
 * @param line the line from the file to be parsed
 * @param delim the character between tokens.
 */
struct _DNSEntry* buildADNSEntryFromALine(char *line, char *token_delim)
{

	char *token;
	struct _IP *ip_struct = malloc(sizeof(struct _IP));
	struct _IP *last_ip_struct;
	struct _DNSEntry* dnsEntry = malloc(sizeof(struct _DNSEntry));
	int firstIP = 1;


	//getting the domain name
	token = strtok(line, token_delim);
	strcpy(dnsEntry->domainName, token);
	dnsEntry->numberOfIPs = 0;

	//getting the Ip's
	while ((token = strtok(NULL, token_delim)) != NULL)
	{
		ip_struct = malloc(sizeof(struct _IP));
		inet_aton((const char*)token, &(ip_struct->IP));
		ip_struct->nextIP = NULL;
		(dnsEntry->numberOfIPs)++;
		if (firstIP == 1)
		{
			dnsEntry->first_ip = ip_struct;
			last_ip_struct = ip_struct;
			firstIP = 0;
		}
		else
		{
			last_ip_struct->nextIP = ip_struct;
			last_ip_struct = ip_struct;
		}
	}

	return dnsEntry;
}

/* Reads a file with the dns information and loads into a _DNSTable structure.
Each line of the file is a DNS entry. 
RETURNS: the DNS table */
struct _DNSTable* loadDNSTableFromFile(char *fileName)
{
	FILE *file;
	char line[1024];
	struct _DNSEntry *dnsEntry;
	struct _DNSEntry *lastDNSEntry;
	struct _DNSTable *dnsTable = malloc(sizeof(struct _DNSTable));
	int firstDNSEntry = 1;

	file = fopen(fileName, "r");
	if (file==NULL)
	{
		perror("Problems opening the file");
		printf("Errno: %d \n", errno);
	}
	else
	{
		//reading the following entries in the file
		while(readLine(file, line, sizeof(line)) != NULL)
		{
			dnsEntry = buildADNSEntryFromALine(line, " ");
			dnsEntry->nextDNSEntry = NULL;
			if (firstDNSEntry == 1)
			{
				dnsTable->first_DNSentry = dnsEntry;
				lastDNSEntry = dnsEntry;
				firstDNSEntry = 0;
			}
			else
			{
				lastDNSEntry->nextDNSEntry = dnsEntry;
				lastDNSEntry = dnsEntry;
			}
		}


		fclose(file);
	}

	return dnsTable;
}


/**
 * Calculates the number of bytes of the DNS table as a byte array format. 
 * It does not  include the message identifier. 
 * @param dnsTable a pointer to the DNSTable in memory.
 */
int getDNSTableSize(struct _DNSTable* dnsTable)
{
	int table_size = 0;
	int numberOfIPs_BYTES_SIZE = sizeof(short);


	struct _DNSEntry *dnsEntry;

	dnsEntry = dnsTable->first_DNSentry;
	if(dnsEntry != NULL)
	{
		do
		{
			table_size +=  ( strlen(dnsEntry->domainName) + SPACE_BYTE_SIZE +
					numberOfIPs_BYTES_SIZE + (dnsEntry->numberOfIPs * sizeof (in_addr_t)) );
		}while((dnsEntry=dnsEntry->nextDNSEntry) != NULL);
	}


	return table_size;
}



/*Return a pointer to the last character copied in next_DNSEntry_ptr + 1 */
/**
 * Converts the DNSEntry passed as a parameter into a byte array pointed by 
 * next_DNSEntry_ptr. The representation will be 
 * domain_name\0number_of_ips[4byte_ip]*]. 
 * @param dnsEntry the DNSEntry to be converted to a Byte Array.
 * @param next_DNSEntry_ptr a pointer to Byte Array where to start copying 
 * the DNSEntry. The pointer moves to the end of the ByteArray representation.
 */
void dnsEntryToByteArray(struct _DNSEntry* dnsEntry, char **next_DNSEntry_ptr)
{

	struct _IP* pIP;

	fflush(stdout);

	strcpy(*next_DNSEntry_ptr, dnsEntry->domainName);
	//we leave one 0 between the name and the number of IP's of the domain
	*next_DNSEntry_ptr += (strlen(dnsEntry->domainName) + 1);
	stshort(dnsEntry->numberOfIPs, *next_DNSEntry_ptr);
	*next_DNSEntry_ptr += sizeof(short);
	if((pIP = dnsEntry->first_ip) != NULL)
	{
		do
		{
			staddr(pIP->IP, *next_DNSEntry_ptr);
			*next_DNSEntry_ptr += sizeof(in_addr_t);
		}while((pIP = pIP->nextIP) != NULL);
	}

}


/*Dumps the dnstable into a byte array*/
/*@Return a pointer to the byte array representing the DNS table */
/*@param dnsTable the table to be serialized into an array of byes */
/*@param _tableSize reference parameter that will be filled with the table size*/
char *dnsTableToByteArray(struct _DNSTable* dnsTable, int *_tableSize)
{ 
	int tableSize = getDNSTableSize(dnsTable);
	*_tableSize = tableSize;

	char *dns_as_byteArray = malloc(tableSize);
	char *next_dns_entry_in_the_dns_byteArray_ptr = dns_as_byteArray;
	struct _DNSEntry *dnsEntry;


	bzero(dns_as_byteArray, tableSize);

	dnsEntry = dnsTable->first_DNSentry;
	do
	{
		dnsEntryToByteArray(dnsEntry, &next_dns_entry_in_the_dns_byteArray_ptr);
	}while((dnsEntry=dnsEntry->nextDNSEntry) != NULL);

	return dns_as_byteArray;

}

/**
 * Function that gets the dns_file name and port options from the program 
 * execution.
 * @param argc the number of execution parameters
 * @param argv the execution parameters
 * @param reference parameter to set the dns_file name.
 * @param reference parameter to set the port. If no port is specified 
 * the DEFAULT_PORT is returned.
 */
int getProgramOptions(int argc, char* argv[], char *dns_file, int *_port){
	int param;
	*_port = DEFAULT_PORT;

	// We process the application execution parameters.
	while((param = getopt(argc, argv, "f:p:")) != -1){
		switch((char) param){		
		case 'f':
			strcpy(dns_file, optarg);
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
struct _DNSEntry* busqueda_domini(struct _DNSTable *dnsTable,char *domini){
	struct _DNSEntry *dnsEntry;
    dnsEntry=dnsTable->first_DNSentry;
	if ((strcmp(domini,dnsEntry->domainName))!=0) {
		dnsEntry=dnsEntry->nextDNSEntry;
		while(dnsEntry!= NULL){
			printf("...");
			if ((strcmp(domini,dnsEntry->domainName))==0){
					break;
			}
			else{
				dnsEntry=dnsEntry->nextDNSEntry;
			}
		}
	}
    return dnsEntry;
}
struct _IP* busqueda_ip(struct _DNSEntry *dnsEntry,struct in_addr ip){
	struct _IP *IP;
    IP=dnsEntry->first_ip;
	if (ip.s_addr!=IP->IP.s_addr) {
		IP=IP->nextIP;
		while(IP!= NULL){
			printf("...");
			if (ip.s_addr==IP->IP.s_addr){
					break;
			}
			else{
				IP=IP->nextIP;
			}
		}
	}
    return IP;
}
void Ok_Operation(int socket){
	char buffer[SIZE_OPCODE];
	printf("Operacio realitzada correctament.\n");
	memset(buffer,'\0',sizeof(buffer));
	stshort(MSG_OP_OK,buffer);
	send(socket,buffer,sizeof(buffer),0);

}
void Error_message(int socket, short Error, short CodeError){
		char *message_error;
		message_error=malloc(sizeof(unsigned short)*2);
		stshort(Error,message_error);
		stshort(CodeError,message_error+2);
		send(socket,message_error,sizeof(message_error),0);
		free(message_error);
}
void process_Hello(int socket){
	char buffer[MAX_BUFF_SIZE];
	memset(buffer,'\0',MAX_BUFF_SIZE);
	stshort(MSG_HELLO,buffer);
	strcpy(buffer+sizeof(unsigned short), "Hola mon!");
	send(socket,buffer,9+2,0);
	printf("buffer: %s\n",buffer+sizeof(unsigned short));
}

/**
 * Function that generates the array of bytes with the dnsTable data and 
 * sends it.
 * @param s the socket connected to the client.
 * @param dnsTable the table with all the domains
 */
void process_LIST_RQ_msg(int sock, struct _DNSTable *dnsTable)
{
	char *dns_table_as_byteArray;
	char *msg;
	int dns_table_size;
	int msg_size = sizeof(short);
	dns_table_as_byteArray = dnsTableToByteArray(dnsTable, &dns_table_size);

	msg_size += dns_table_size;

	msg = malloc(msg_size);
	stshort(MSG_LIST,msg);
	memcpy(msg + sizeof(short), dns_table_as_byteArray, dns_table_size);
	send(sock,msg,msg_size,0);
}

void process_IP_LIST(int sock, struct _DNSTable *dnsTable, char *domini){

	struct _DNSEntry *dnsEntry;
	struct _IP *pIP;
	fflush(stdout);
	printf("Buscant el domini %s ...",domini);
	dnsEntry=busqueda_domini(dnsTable,domini);
	if(dnsEntry!=NULL){
		int offset=0;
		char *buffer;
		printf("\nS'ha trobat el domini %s\n",domini);
		buffer=malloc(sizeof(unsigned short)+(sizeof(struct in_addr)*dnsEntry->numberOfIPs));
		stshort(MSG_IP_LIST,buffer);
		offset+=sizeof(unsigned short);
		printf("Buscant les seves IPs ...");
		if((pIP = dnsEntry->first_ip) != NULL)
		{
			do
			{
			  printf("...");
			  staddr(pIP->IP,buffer+offset);
			  offset+=sizeof(struct in_addr);
			}while((pIP = pIP->nextIP) != NULL);
			int i=2;
			printf("\nTransmisio de les Ips del domini %s:",domini);
			while(i<offset){
			 printf(" %s ",inet_ntoa(ldaddr(buffer+i)));
			 i+=sizeof(struct in_addr);
			}
			printf("\n");
			send(sock,buffer,offset,0);
			free(buffer);
		}
	}else{
		printf("\nNo s'ha trobat el domini %s\n",domini);
		Error_message(sock,MSG_OP_ERR,ERR_2);
	}
}

void process_AddDomain(int sock, struct _DNSTable *dnsTable, char *buffer,int tamany){

    int offset=0;
    char domini[50];
    struct _DNSEntry *dnsEntry=malloc(sizeof(struct _DNSEntry));
    struct _DNSEntry *aux;
    struct _IP *IP=malloc(sizeof(struct _IP));
    struct _IP *pIP;
    memset(domini,'\0',sizeof(domini));
    fflush(stdout);
	offset+=sizeof(unsigned short);
	strcpy(domini,buffer+offset);
	offset+=(strlen(domini)+1);
	int cantidad_ips=(tamany-offset)/sizeof(struct in_addr);
	printf("Cantidad de ips %d\n",cantidad_ips);
	printf("Buscant el domini.... %s\n",domini);
	if (cantidad_ips == 1){
		printf("Cantidad de ips 1\n");
		if ((aux=busqueda_domini(dnsTable,domini))!=NULL){
			printf("S'ha trobat el domini %s\n",domini);
			printf("Afegint IPS al domini %s....\n",domini);
			IP->IP=ldaddr(buffer+offset);
			offset+=sizeof(struct in_addr);
			IP->nextIP=NULL;
			pIP=aux->first_ip;
			aux->first_ip=IP;
			aux->first_ip->nextIP=pIP;
			(aux->numberOfIPs)++;
		}else{
			printf("No s'ha trobat el domini %s\n",domini);
			printf("Afegint el nou domini %s\n",domini);
			printf("Afegint IPS al domini %s....\n",domini);
			strcpy(dnsEntry->domainName,domini);
			IP->IP=ldaddr(buffer+offset);
			offset+=sizeof(struct in_addr);
			IP->nextIP=NULL;
			dnsEntry->first_ip=IP;
			dnsEntry->numberOfIPs=0;
			(dnsEntry->numberOfIPs)++;
			aux=dnsTable->first_DNSentry;
			dnsTable->first_DNSentry=dnsEntry;
			dnsEntry->nextDNSEntry=aux;
		}
	}else if(cantidad_ips>1){
		printf("Cantidad de ips superior a 1\n");
		if ((aux=busqueda_domini(dnsTable,domini))!=NULL){
			printf("S'ha trobat el domini %s\n",domini);
			printf("Afegint IPS al domini %s....\n",domini);

			while(cantidad_ips>0){
				IP=malloc(sizeof(struct _IP));
				IP->IP=ldaddr(buffer+offset);
				offset+=sizeof(struct in_addr);
				IP->nextIP=NULL;
				pIP=aux->first_ip;
				aux->first_ip=IP;
				aux->first_ip->nextIP=pIP;
				(aux->numberOfIPs)++;
				cantidad_ips--;
			}
		}else{
			printf("No s'ha trobat el domini %s\n",domini);
			printf("Afegint el nou domini %s\n",domini);
			printf("Afegint IPS al domini %s....\n",domini);
			strcpy(dnsEntry->domainName,domini);
			dnsEntry->numberOfIPs=0;
			int firstIP=1;
			while(cantidad_ips>0){
				IP=malloc(sizeof(struct _IP));
				IP->IP=ldaddr(buffer+offset);
				offset+=sizeof(struct in_addr);
				IP->nextIP=NULL;
				if (firstIP == 1){
					dnsEntry->first_ip = IP;
					pIP = IP;
					firstIP = 0;
				}else{
					pIP->nextIP = IP;
					pIP = IP;
				}
				cantidad_ips--;
				(dnsEntry->numberOfIPs)++;

			}
			aux=dnsTable->first_DNSentry;
			dnsTable->first_DNSentry=dnsEntry;
			dnsEntry->nextDNSEntry=aux;
		}
	}
	Ok_Operation(sock);
}

void process_change_domain(int sock, char* buffer, struct _DNSTable *dnsTable){
	struct _DNSEntry *aux;
	struct _IP *IP;
	struct in_addr address_antigua;
	struct in_addr address_nueva;
   	int offset=sizeof(unsigned short);
	char domini[100];
	memset(domini,'\0',sizeof(domini));
	strcpy(domini,buffer+offset);
	offset+=(strlen(domini)+1);
	address_antigua=ldaddr(buffer+offset);
	offset+=sizeof(struct in_addr);
	address_nueva=ldaddr(buffer+offset);
	if((aux=busqueda_domini(dnsTable,domini))!=NULL){
		printf("S'ha trobat el domini %s\n",domini);
		if((IP=busqueda_ip(aux,address_antigua))!=NULL){
			printf("S'ha trobat la ip %s dins del domini %s\n",inet_ntoa(address_antigua),domini);
			printf("Modificant la ip %s per la ip ",inet_ntoa(address_antigua));
			printf("%s del domini %s\n" ,inet_ntoa(address_nueva),domini);
			IP->IP=address_nueva;
			Ok_Operation(sock);
		}else{
			printf("\nNo existeix la ip %s dins del domini %s\n",inet_ntoa(address_antigua),domini);
			Error_message(sock,MSG_OP_ERR,ERR_1);
		}		
	}else{
		printf("\nNo s'ha trobat el domini %s\n",domini);
		Error_message(sock,MSG_OP_ERR,ERR_2);
	}
}
/** 
 * Receives and process the request from a client.
 * @param s the socket connected to the client.
 * @param dnsTable the table with all the domains
 * @return 1 if the user has exit the client application therefore the 
 * connection whith the client has to be closed. 0 if the user is still 
 * interacting with the client application.
 */
int process_msg(int sock, struct _DNSTable *dnsTable)
{
	unsigned short op_code;
	char buffer[MAX_BUFF_SIZE];
	int done = 0;
	int size;
	memset(buffer,'\0',sizeof(buffer));

	if ((size=recv(sock,buffer,sizeof(buffer),0))==ERROR){
		perror("Error al recibir el mensaje");
		return (EXIT_FAILURE);
	}
	op_code=ldshort(buffer);
	printf("Opcode recibido: %i\n", op_code);
	switch(op_code)
	{
	case MSG_HELLO_RQ:
		process_Hello(sock);
		break;
	case MSG_LIST_RQ:
		process_LIST_RQ_msg(sock, dnsTable);
		break;
	case MSG_DOMAIN_RQ:
		process_IP_LIST(sock, dnsTable,buffer+2);
		break;
	case MSG_ADD_DOMAIN:
		process_AddDomain(sock, dnsTable,buffer,size);
		break;
	case MSG_CHANGE_DOMAIN:
		process_change_domain(sock,buffer,dnsTable);
		break;
	case MSG_FINISH:
		printf("Finalització de la connexio amb el client\n");
		done = 1;
		break;
	default:
		perror("Message code does not exist.\n");
	}

	return done;
}

int main (int argc, char * argv[]){
	struct _DNSTable *dnsTable;
	int port ;
	char dns_file[MAX_FILE_NAME_SIZE] ;
	int finish = 0;
	struct sockaddr_in server;
	struct sockaddr_in client;
	int socket_servidor;
	int socket_clientes;
	int process;

	getProgramOptions(argc, argv, dns_file, &port);

	dnsTable = loadDNSTableFromFile(dns_file);
	printDNSTable(dnsTable);

	//TODO: setting up the socket for communication

	//creacio del socket del servidor
	if((socket_servidor=socket(PF_INET, SOCK_STREAM, IPPROTO_TCP))==ERROR){
		perror("Error al crear el socket del servidor");
		return EXIT_FAILURE;
	}
	server.sin_family=AF_INET;
	server.sin_port=htons(port);
	server.sin_addr.s_addr=htonl(INADDR_ANY);

	if(bind(socket_servidor, (struct sockaddr*)&server, sizeof(server))==ERROR){
		perror("Error al enllaçar la estructura del server amb el socket");
		return EXIT_FAILURE;
	}
	if(listen(socket_servidor,3)==ERROR){
		perror("Error en la funcio listen");
		return EXIT_FAILURE;
	}

	socklen_t addrlen = sizeof(client);
	while(1) {
		if ((socket_clientes = accept(socket_servidor, (struct sockaddr *)&client, &addrlen)) == ERROR){
			perror("Error en la funcio accept");
			return EXIT_FAILURE;
		}

		printf("Se obtuvo una conexión desde: %s\n", inet_ntoa(client.sin_addr));
		process=fork();
		if (process==ERROR){
			perror("Error en la creacio del proces fill\n");
			exit (EXIT_FAILURE);
		}
		if (process==PROCESO_HIJO){
			do{
				finish = process_msg(socket_clientes, dnsTable);
			}while(finish==0);
			close(socket_clientes);
			exit(EXIT_SUCCESS);
		}
	}
	exit(EXIT_SUCCESS);
	return 0;
}


