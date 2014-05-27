#include <Tlc5940.h>
#include <tlc_config.h>

#include <SPI.h>
#include <Ethernet.h>
#include <TextFinder.h>
 
byte Mac[] =    { 0x90, 0xA2, 0xDA, 0x0D, 0x50, 0x91 };
char host[] = "arbre-a-tweets.cestdoncvrai.fr";
char nbtweet[10];
EthernetClient client;
String shield_id = "rany";

const int tweetMax = 16; //nombre de leds sur la carte
int intMax = 4095; 
int intensityDim = 1000;
int diodes[tweetMax];
 
void setup()
{
  Tlc.init();
  Serial.begin(9600);
  Ethernet.begin(Mac);
  delay(1000);
}

void loop()
{
  int i;
  Serial.println("Connecting to server...");
 
  if (client.connect(host, 80))
  {
    Serial.println("Connected");
    client.println("GET /~cloe/arbre-tweets/server/api.php?domain=cheat_tweets_count&shield_id="+shield_id+"&card=ard HTTP/1.0");
    client.println("Connection: close");
    client.print("Host: ");
    client.println(host);
    client.println("User-Agent: Arduino/1.0");
    client.println();
 
    TextFinder finder(client);
 
    while (client.connected())
    {
      if (client.available())
      {
        Serial.println("Client available");
        Serial.println("Searching for the last tweet...");
        if ( finder.getString("<nbtweets>", "</nbtweets>", nbtweet, 10) != 0 )
        {
          displayLedByTweetsCount(nbtweet);
           Serial.println(nbtweet);
        }
        Serial.println("End of finder");
      }
    }
    delay(1);
    client.stop();
    Serial.println("Client stopped");
 
  }
 
  else { Serial.println("Connection failed"); }
 
  delay (1000);
 
}


void displayLedByTweetsCount(String nbTweetsMore)
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
 Tlc.update();
}
