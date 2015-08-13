
Modifications of librairies to use Ethernet Shield and Tlc5940 together :

Tlc5940/tlc_config.h : 

61.	#define DATA_TRANSFER_MODE    TLC_BITBANG


71.	#if DATA_TRANSFER_MODE == TLC_BITBANG
72.		/** SIN (TLC pin 26) */
73.		#define SIN_PIN        PD7
74.		#define SIN_PORT       PORTD
75.		#define SIN_DDR        DDRD
76.		/** SCLK (TLC pin 25) */
77.		#define SCLK_PIN       PD4
78.		#define SCLK_PORT      PORTD
79.		#define SCLK_DDR       DDRD
80.	#endif