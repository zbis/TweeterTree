#ifndef network_h
#define network_h

#include <SPI.h>
#include <Ethernet.h>
#include "ressources.h"

class Network
{
public:
	Network();
	void networkInitialization(void);
	void serverConnection(void);
	boolean checkTweet();
	boolean getConnectedToServer(void){ return connectedToServer; }

private:
	EthernetClient client;
	IPAddress serverIPAddress;
	IPAddress arduinoIPAddress;

	byte arduinoMacAddress[];

	boolean connectedToServer;
	boolean tweetIsPresent;

	int serverPort;
	int delayBeforeReboot;

	String tweetCheckAddress;
	String line;
	String keyWordTweet;

};
#endif