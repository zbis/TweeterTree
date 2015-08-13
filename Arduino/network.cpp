/*
 *  network.cpp: Library to receive information about a server
 *
 */
#include "network.h"

Network::Network(){
	serverIPAddress = {192,168,0,20};
	serverPort = 80;
	connectedToServer = false;
	byte arduinoMacAddress[] = { 0x00, 0xAA, 0xBB, 0xCC, 0xDE, 0x02 };
    boolean tweetIsPresent = false;
    tweetCheckAddress = "/getTweet.php";
    keyWordTweet = "TWEET";
    delayBeforeReboot = 0;
}
/*
	Initialization of the Arduino network shield
*/
void Network::networkInitialization(){
	if (Ethernet.begin(arduinoMacAddress) == 0) {
        Serial.println("Failed to configure Ethernet using DHCP");
    }
    arduinoIPAddress = Ethernet.localIP();

    Serial.print("Arduino Ethernet Shield Address : ");
    Serial.println(arduinoIPAddress);
    delay(1000);
}
/*
	Server connection, change the value of connectedToServer from false to true if the connection suceed.
*/
void Network::serverConnection() {

    Serial.print("connection to the server ");
    Serial.println(serverIPAddress);

    if (client.connect(serverIPAddress,serverPort)) {
        connectedToServer = true;
        Serial.println("Connected ! ");
       
    } else {
        connectedToServer = false;
        Serial.println("connection failed");
    }
}
/*
    Check if a new tweet is present on the server
*/
boolean Network::checkTweet() {
   
    client.print("GET ");
    client.print(tweetCheckAddress);
    client.println(" HTTP/1.1");

    client.print("Host: ");
    client.println(serverIPAddress);

    client.println("Connection: Close");
    client.println();

    if(delayBeforeReboot >= 5000){
        //If after 5000 loops no tweet was found, reboot.
        Ressources::reboot();
    }
    if (client.connected() && client.available()) {
        char c = client.read();
        line += c;
    }
    if (line.indexOf(keyWordTweet) != -1){
        tweetIsPresent = true;
    }

    delayBeforeReboot++;
    delay(1);

    return tweetIsPresent;
}