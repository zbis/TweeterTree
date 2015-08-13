#include "ressources.h"
/*
	Allumage des leds
*/
void Ressources::reboot(){
	wdt_enable(WDTO_15MS);
	while(1){}
}