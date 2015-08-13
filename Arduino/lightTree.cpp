/*
 *  network.cpp: Library to switch on the leds following a schema.
 *
 */
#include "lightsTree.h"

/*
	Initialization of the leds and play a schema of leds until leaveFunction reach 100
*/
void LightsTree::lightsTree(){
    Tlc.init();

    int direction = 1;
  	int leaveFunction = 0;

  	for(int channel = 0; channel < NUM_TLCS * 16; channel += direction) {
	    leaveFunction+=1;
	    Tlc.clear();
	    if (channel == 0) {
	    	direction = 1;
	    }else{
	      	Tlc.set(channel - 1, 1000);
	    }
	    Tlc.set(channel, 4095);
	    if (channel != NUM_TLCS * 16 - 1) {
	    	Tlc.set(channel + 1, 1000);
	    }else{
	    	direction = -1;
	    }
	    Tlc.update();
		delay(75);
	    if(leaveFunction>=100){
	    	//After 100 loops, reboot of the arduino 
	     	Ressources::reboot();
	    }
  	}
}
