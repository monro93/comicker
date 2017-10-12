# Comicker
Telegram bot that downloads and sends comics periodically.

This bot works as a comic list where you can download and read comics that launch weekly or periodically.
If you don't share the comics with anyone it shouldn't be ilegal, it doesn't host any comic, it only crawls it from the web, zip it,
and send it via Telegram.

I haven't tested it with chat groups, but theorically it should work.

## Configuration

To make it work you have to include the API code from the telegram bot at [Settings](./Resources/settings.yml) and also the chat id of the conversation with your bot (see [Telegram API](https://core.telegram.org/bots/api) for more info). The chat id is one of the things I want to change to do it more dinamic. 

## Adding Comics
The comics are added in the file [Comics](./Resources/comics.yml). The structure is pretty simple, use the example to do it. The name determines the zip name.

## Supported sites

* [Read Comics Tv](http://www.readcomics.tv/) (looks closed now, I don't know if it's permanent)
* [Tu Manga Online](https://www.tumangaonline.com/home)

## Runnig the program
You don't even need a php server, with php-cli should be enought. I recommend to use a cron to automize the job (For instance, I have to code running in rasberry pi and hourly it runs the program)

`php pathTo/Comicker.php ` 

## Future Steps
I have it a bit abandoned it, but maybe I do some implementantions and I will accept Pull Request too. Some of the thing that I would like to do are:

* Add more sites, it should be extremly easy
* Add other message services
* Have some user system to save comics preferences, lecture list, etc
* Improve bot interaction 
    * Add a way to add and remove comics via telegram
