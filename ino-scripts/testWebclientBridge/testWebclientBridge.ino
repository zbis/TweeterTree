#include <TextFinder.h>
#include <Bridge.h>
#include <HttpClient.h>
#include <Console.h>

#include <Tlc5940.h>
#include <tlc_animations.h>
#include <tlc_config.h>
#include <tlc_fades.h>
#include <tlc_progmem_utils.h>
#include <tlc_shifts.h>



String shield_id = "rany";
String APIURL = "http://arbre-a-tweets.cestdoncvrai.fr/api.php";
int intMax = 4095; 

const int tweetMax = 16; //nombre de leds sur la carte
int intensityDim = 1000;
int diodes[tweetMax];
char nbtweet[10];
int frequence;
String jsonstring;


void setup() {
  Bridge.begin();
  Console.begin();
  while (!Console){
    ; // wait for Console port to connect.
  }
  Console.println("You're connected to the Console!!!!");
  Tlc.init();
  /* Récupération des configurations de la carte */
  HttpClient client;
  client.get(APIURL+"?domain=shield&shield_id="+shield_id+"&card=ard");
  
  while (client.available())
  {
      TextFinder finder(client);
      Console.println(APIURL+"?domain=shield&shield_id="+shield_id+"&card=ard");
      Console.println("Searching for shield parameters");
      char sTweetMax[10];
      finder.getString("<led_count>", "</led_count>", sTweetMax, 10);
      char sFrequence[10000];
      finder.getString("<blinking_time>", "</blinking_time>", sFrequence, 100);   
      Console.println(int(sFrequence));
      Console.println(sTweetMax);
  }
}

void loop() {
  HttpClient client;
  client.get(APIURL+"?domain=cheat_tweets_count&shield_id="+shield_id+"&card=ard");
  while (client.available())
  {
      TextFinder finder(client);
      Console.println("Searching for the last tweet...");
      finder.getString("<tweets_count>", "</tweets_count>", nbtweet, 10);
      
      if (nbtweet != 0 )
      {
        finder.getString("<cheat_tweets_count>", "</cheat_tweets_count>", nbtweet, 10);
      }
      if(nbtweet != 0 ) 
      {
        displayLedByTweetsCount(nbtweet);
        Console.println(nbtweet);
        while(Tlc.update()); 
      }
      Console.println("End of finder");    
  }
  Console.flush();

  delay(3000);
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
}

