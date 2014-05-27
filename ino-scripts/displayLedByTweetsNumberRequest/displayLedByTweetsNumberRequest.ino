//ARDUINO 1.0+ ONLY
//ARDUINO 1.0+ ONLY
#include <Ethernet.h>
#include <SPI.h>
#include "tlc_config.h"
#include "Tlc5940.h"

////////////////////////////////////////////////////////////////////////
//CONFIGURE
////////////////////////////////////////////////////////////////////////
byte server[] =  { 88,191,185,111 }; //ip Address of the server you will connect to

//The location to go to on the server
//make sure to keep HTTP/1.0 at the end, this is telling it what type of file it is
String location = "/~cloe/arbre-tweets/server/api.php?domain=cheat_tweets_count&shield_id=rany HTTP/1.0";


// if need to change the MAC address (Very Rare)
byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
////////////////////////////////////////////////////////////////////////

EthernetClient client;

char inString[32]; // string for incoming serial data
int stringPos = 0; // string index counter
boolean startRead = false; // is reading?

const int tweetMax = 16; //nombre de leds sur la carte
int intMax = 4095;
int updateDelay = 3;
int intensityDim = 1000;
int diodes[tweetMax];

unsigned long lastConnectionTime = 0;          // last time you connected to the server, in milliseconds
boolean lastConnected = false;                 // state of the connection last time through the main loop
const unsigned long postingInterval = 10*1000;  // delay between updates, in milliseconds

void setup(){
  Ethernet.begin(mac);
  Serial.begin(9600);
}

void loop(){
  String pageValue = connectAndRead(); //connect to the server and read the output
  if(pageValue !=  "failed") {
    displayLedByRandom(pageValue);
    Tlc.update();
  }
  Serial.println(pageValue); //print out the findings.
  delay(5000);
}

String connectAndRead(){
  //connect to the server

  Serial.println("connecting...");

  //port 80 is typical of a www page
  if (client.connect(server, 80)) {
    Serial.println("connected");
    client.print("GET ");
    client.println(location);
    client.println();
    //Connected - Read the page
    return readPage(); //go and read the output
  }else{
    return "failed";
  }

}

String readPage(){
  //read the page, and capture & return everything between '<' and '>'

  stringPos = 0;
  memset( &inString, 0, 32 ); //clear inString memory

  while(true){

    if (client.available()) {
      char c = client.read();

      if (c == '<' ) { //'<' is our begining character
        startRead = true; //Ready to start reading the part 
      }else if(startRead){

        if(c != '>'){ //'>' is our ending character
          inString[stringPos] = c;
          stringPos ++;
        }else{
          //got what we need here! We can disconnect now
          startRead = false;
          client.stop();
          client.flush();
          Serial.println("disconnecting.");
          return inString;

        }

      }
    }

  }

}
void displayLedByRandom(String nbTweetsMore)
{
  int nbTweetMore = nbTweetsMore.toInt();
  Serial.print("Tweets since last update : ");
  Serial.println(nbTweetMore);
  Serial.println("\\/ Decresed Led");
  // First decresed all diodes intensity 
  decresedLeds();

  Serial.println("/\\ Up Led intensity");
  upLeds(nbTweetMore);
  
  Serial.println(" (*) Display Led");
  //then set led intensity
  displayLeds();

}

int calcMoy()
{
  long totalIntensity = 0;
  for (int a = 0; a < tweetMax; a++)
  {
    if(diodes[a] != 0)
    {
      totalIntensity = totalIntensity + diodes[a];
    }
  }
  return (totalIntensity/tweetMax);
}

void decresedLeds()
{
  for (int x = 0; x < tweetMax; x++)
  {
    
    if(diodes[x] > 0)
    {
      diodes[x] = diodes[x] - intensityDim;
    }
    if (diodes[x] < 0)
    {
      diodes[x] = 0;
    }
  }
 
}
void upLeds(int nbLeds)
{
  for(int y = 0; y < nbLeds; y ++) 
  {
    randomSeed(analogRead(0));
    boolean endofwhile = false;
    int moy = 0;
    moy = calcMoy();
    
    int displayled = 0;
    while (!endofwhile)
    {
      int randomIndex = random(0, tweetMax);
      randomIndex = randomIndex;
      if(diodes[randomIndex] <= moy)
      {
        diodes[randomIndex] = intMax;
        endofwhile = true;
        displayled = randomIndex;
      }
    }
  }
}
void displayLeds()
{
  for (int z = 0; z < tweetMax; z++)
  {
    Tlc.set(z, diodes[z]);
  }
}
