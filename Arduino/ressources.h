#ifndef ressources_h
#define ressources_h

#include <avr/wdt.h>

class Ressources
{
public:
	Ressources();
	static void reboot();
};

#endif