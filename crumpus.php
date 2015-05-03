#!/usr/bin/php
<?php
// TODO: Weitere Todos definieren
// TODO: Nicht Himmelsrichtungen zeigen, sondern ob es nach rechts/links/vor/zurueck geht.

class tPlayer
{
   function __construct($name)
   {
      $this->name = $name;
      $this->roomId = 0;
   }

   function setRoom($room)
   {
      $this->setRoomId($room->getId());
   }

   function setRoomId($id)
   {
      $this->roomId = $id;
   }

   function getRoomId()
   {
      return $this->roomId;
   }
}

class tRoom
{
   function __construct( $id, $name = null)
   {
      $this->id = $id;
      $this->name = $name;
      $this->description = null;
      $this->northId = null;
      $this->southId = null;
      $this->eastId  = null;
      $this->westId  = null;
      $this->items = [];
   }

   function setDescription($txt)
   {
      $this->description = $txt;
   }

   function getDescription()
   {
      return $this->description;
   }

   function hasDescription()
   {
      return $this->getDescription() !== null;
   }

   function getId()
   {
      return $this->id;
   }

   function addItem($name)
   {
      $this->items[] = $name;
   }

   function connect($direction, tRoom $oth)
   {
      switch ($direction)
      {
         case "east":
            $this->eastId = $oth->getId();
            $oth->westId = $this->getId();
            break;
         case "west":
            $this->westId = $oth->getId();
            $oth->eastId = $this->getId();
            break;
         case "north":
            $this->northId= $oth->getId();
            $oth->southId = $this->getId();
            break;
         case "south":
            $this->southId = $oth->getId();
            $oth->northId = $this->getId();
            break;
         default: throw new Exception("unknown direction '$direction'");
      }
   }

   function getShortInfo()
   {
      return $this->getId() . ' (' . ($this->name !== null ? '"' . $this->name . '"': 'NULL') . ')';
   }

   function getWhereCanIGo()
   {
      $wcig = [];
      if ($this->westId  !== null) $wcig[] = '(w)esten';
      if ($this->northId !== null) $wcig[] = '(n)orden';
      if ($this->eastId  !== null) $wcig[] = '(o)sten';
      if ($this->southId !== null) $wcig[] = '(s)ueden';
      return implode(", ", $wcig);
   }

   function dump()
   {
       print "Room " . $this->getShortInfo() . "\n";
       print "  description: " . ($this->hasDescription() ? $this->getDescription() : 'NONE') . "\n";
       print "  east : " . var_export($this->eastId, 1) . "\n";
       print "  west : " . var_export($this->westId, 1) . "\n";
       print "  north: " . var_export($this->northId, 1) . "\n";
       print "  south: " . var_export($this->southId, 1) . "\n";
   }
}


// Create the world:

class tGame
{
   function __construct()
   {
      $this->maxRoomId = -1;
      $this->rooms = []; // id => object
      $this->player = new tPlayer('t3o');
      $this->initRooms();
      $this->player->setRoomId( 0 );
   }

   function initRooms()
   {
      // test: four connected rooms:
      $root = $this->addRoom('root');
      $root->addItem('eine alte geschlossene Holzkiste.');
      $root->setDescription("Du befindest dich in einem düsteren, feuchten Gewölbe.");

      $tmp = $this->addRoom('A room', 'east', $root);
      $tmp->addItem('eine leere Glasflaschea');
      $tmp = $this->addRoom('A room', 'north', $tmp);
      $tmp->addItem('einen Kronkorken');
      $tmp = $this->addRoom('A room', 'west', $tmp);
      $tmp->connect('south', $root);
   }

   function getNewId()
   {
      $id = $this->maxRoomId + 1;
      $this->maxRoomId = $id;
      return $id;
   }

   function addRoom( $name = null, $inDirection = null, $ofRoom = null)
   {
      $id = $this->getNewId();
      $r = new tRoom($id, $name);
      $this->rooms[ $r->getId() ] = $r;

      if (($inDirection !== null) && ($ofRoom !== null))
      {
          $ofRoom->connect($inDirection, $r);
      }
      return $r;
   }

   function dump()
   {
       print "Gamedump:\n";
       print "Max Id: " . $this->maxRoomId . "\n";
       foreach ($this->rooms as $id => $r)
       {
          $r->dump();
       }
   }

   function getPlayer()
   {
      return $this->player;
   }

   function getPlayerRoom()
   {
      return $this->rooms[ $this->player->getRoomId() ];
   }
}

$game = new tGame();

print "Hallo Spieler!\n";
$cmd = null;
while ($cmd !== 'q')
{
    $pr = $game->getPlayerRoom();
    print "\n";
    print "Raum " . $pr->getId() . ".\n";
    if ($pr->hasDescription()) print "\n" . $pr->getDescription() . "\n";
    if (count($pr->items))
    {
       print "Du siehst " . implode(", ", $pr->items) . ".\n";
    }
    
    print "Von hier aus geht es weiter nach: " . $pr->getWhereCanIGo() . "\n";
    print "\n";

    $cmd = readline('Deine Eingabe (h = help) > ');

    switch ($cmd)
    {
       case "n" :
          if ($pr->northId !== null)
          {
             $game->player->setRoomId($pr->northId);
          }
          break;
       case "s" :
          if ($pr->southId !== null)
          {
             $game->player->setRoomId( $pr->southId );
          }
          break;
       case "e" :
          if ($pr->eastId !== null)
          {
             $game->player->setRoomId( $pr->eastId );
          }
          break;
       case "w" :
          if ($pr->westId !== null)
          {
             $game->player->setRoomId( $pr->westId );
          }
          break;
       case "h":
          print "Gehe nach (n)orden, (s)ueden, (o)sten oder (w)esten, oder Beende mit (q)uit.\n";
          break;
       case "dump":
          $game->dump();
          break;
    }
}

