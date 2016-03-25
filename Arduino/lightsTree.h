#ifndef lightsTree_h
#define lightsTree_h

#include <SPI.h>
#include "Adafruit_WS2801.h"
#include "ressources.h"

class LightsTree
{
public:
	void lightsTree();
	void init();
	Adafruit_WS2801 strip;
};
#endif
