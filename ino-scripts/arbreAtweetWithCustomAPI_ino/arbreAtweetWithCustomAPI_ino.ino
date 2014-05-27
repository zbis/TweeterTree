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



String shield_id = "2ansfaclab";
String APIURL = "http://arbre-a-tweets.cestdoncvrai.fr/api.php";
int intMax = 4095; 

const int tweetMax = 16; //nombre de leds sur la carte
int intensityDim = 200;
int diodes[tweetMax];
char nbtweet[10];
char frequence[10];
char ledcount[10];
long tdelay = 30000;
String jsonstring;
bool firstconf = false;
int delaycount = 0;
int isfirst = true;

TLC_CHANNEL_TYPE channel;

void setup() {
  
  Bridge.begin();
  Console.begin();
  while (!Console){
    ; // wait for Console port to connect.
  }
  Tlc.init();
  Tlc.setAll(0);
  while(Tlc.update());
}

void loop() {
  HttpClient client;
  if(firstconf == false)
  {
    Console.println("First configuration");
    client.get(APIURL+"?domain=shield&shield_id="+shield_id+"&card=ard");
    Console.println("Searching for the shield");
     if (client.available())
      {
            TextFinder finder(client);
            char hastag[100];
            finder.getString("<blinking_time>", "</blinking_time>", frequence, 10);
            finder.getString("<led_count>", "</led_count>", ledcount, 10);
            finder.getString("<hashtags>", "</hashtags>", hastag, 10);
            tdelay = atol(frequence);
            int tweetMaxI = atoi(ledcount);
            diodes[tweetMaxI];
            Console.println("Configure shield with : ");
            Console.print("Leds :");
            Console.println(tweetMaxI);
            Console.print("Frequence :");
            Console.println(frequence);
            Console.print("Hashtag : ");
            Console.println(hastag);
            firstconf = true;
      }
      delay(10);
  } else {
        client.get(APIURL+"?domain=tweets_count&shield_id="+shield_id+"&card=ard");
        //client.get(APIURL+"?domain=cheat_tweets_count&shield_id="+shield_id+"&card=ard");
        if (client.available())
        {
           TextFinder finder(client);
            Console.println("Searching for the last tweet...");
            finder.getString("<tweets_count>", "</tweets_count>", nbtweet, 10);
            //finder.getString("<cheat_tweets_count>", "</cheat_tweets_count>", nbtweet, 10);
            if(nbtweet != 0 ) 
            {
              if(isfirst==true) {
                  Tlc.setAll(3000);
                  delay(500);
                  while(Tlc.update());
                  Tlc.setAll(0);
                  isfirst = false;
                } else {
                   displayLedByTweetsCount(nbtweet);
                   Console.println(nbtweet);
                    while(Tlc.update());
                  }
            }
            Console.println("End of finder");
        }
    }
    delay(tdelay);
  Console.flush();
}


void displayLedByTweetsCount(String nbTweetsMore)
{
  int nbTweetMore = nbTweetsMore.toInt();
  Console.print("Tweets since last update : ");
  Console.println(nbTweetMore);
  Console.println("\\/ Decresed Led");
  // First decresed all diodes intensity 
  decresedLeds();

  Console.println("/\\ Up Led intensity");
  upLeds(nbTweetMore);
  
  Console.println(" (*) Display Led");
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
  while(Tlc.update()); 
}

