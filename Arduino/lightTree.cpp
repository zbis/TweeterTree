/*
 *	network.cpp: Library to switch on the leds following a schema.
 *
 */
#include "lightsTree.h"

void LightsTree::init() {
  Serial.println("WS2801 started !");
	uint8_t dataPin	 = 2;	 // Yellow wire on Adafruit Pixels
	uint8_t clockPin = 3;	 // Green wire on Adafruit Pixels
	strip = Adafruit_WS2801(25, dataPin, clockPin);
	strip.begin();
	strip.show();
	randomSeed(analogRead(0));
}
/*
	Initialization of the leds and play a schema of leds until leaveFunction reach 100
*/
void LightsTree::lightsTree(){
Serial.println("lights !");
	int direction = 1;
	int leaveFunction = 0;
	

	for(int channel = 0; channel < 1000; channel += direction) {
		leaveFunction+=1;
		int randNumber = random(0, 24);
		for(int i=0;i<25;i++){
			strip.setPixelColor(i, 0, 0, 0);
		}
		strip.setPixelColor(randNumber, 0, 0, 255);
		strip.show();
		delay(75);
		if(leaveFunction>=1000){
			//After 100 loops, reboot of the arduino 
			Ressources::reboot();
		}
	}
}
