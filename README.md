
TweeterTree
=============

TweeterTree is a physical fake tree which illuminate when a hashtag(#) is send on Twitter. 

Material:
-------

* Arduino Uno
* Ethernet shield
* Tlc5940
* A pc use like a server with a WAMP to check hashtag

Illustrations:
-------

![Tree](https://raw.github.com/zbis/TweeterTree/design/tree.jpg)
![Card](https://raw.github.com/zbis/TweeterTree/design/elec.jpg)
Modifications of librairies to use Ethernet Shield and Tlc5940 together :
-------

Tlc5940/tlc_config.h :
-------

Modification for the data transfer mode from TLC_SPI to TLC_BITBANG and modification of SIN and SCLK PINS from default to use on the TLC5940 PIN 7 FOR SIN and PIN 4 FOR SCLK.

```c++
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
```

Modifications on the server :
-------
Change php.ini from : 

```
[curl]
; A default value for the CURLOPT_CAINFO option. This is required to be an
; absolute path.
;curl.cainfo = 
```

to 

```
[curl]
; A default value for the CURLOPT_CAINFO option. This is required to be an
; absolute path.
curl.cainfo = "C:\wamp\bin\php\certificate\cacert.pem"
```

and add the cacert.pem file in the correct folder.
