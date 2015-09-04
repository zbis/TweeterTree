
#include "network.h"
#include "lightsTree.h"

Network ethernetShield;
LightsTree tree; 

/*
Areas for improvements
	Allow to checkTweet all the time without reboot the Arduino.
	Change the code of tree.lightsTree 
	Allow to stop the checkTweet function when the webpage is finished.
	Able to add decoration function like musicTree etc.. easily juste by adding a function and call it in main.
*/
void setup() {
	Serial.begin(9600);
	ethernetShield.networkInitialization();
	ethernetShield.serverConnection();
}

void loop() {
	if(ethernetShield.getConnectedToServer()){
		if(ethernetShield.checkTweet()){
		 	tree.lightsTree();
		}
	}
}