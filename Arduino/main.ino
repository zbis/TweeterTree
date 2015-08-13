#include "network.h"
#include "lightsTree.h"

Network ethernetShield;
LightsTree tree; 

void setup() {
	Serial.begin(9600);

	ethernetShield.networkInitialization();
	ethernetShield.serverConnection();
}

void loop() {
	if(ethernetShield.getConnectedToServer()){
		if(ethernetShield.checkTweet()){
		 	Serial.println("Ok!");
		 	tree.lightsTree();
		}
	}
}