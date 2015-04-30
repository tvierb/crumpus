#!/usr/bin/php
<?php

$rooms = [];
class tPlayer
{
   function __construct($name)
   {
      $this->name = $name;
      $this->roomId = 0;

   }

   function setRoom($id)
   {
      $this->roomId = $id;
   }
}

/**
 * find highest room id
 *
 * @return int $maxId
 */
function getHighRoomId()
{
  global $rooms;
  $maxId = -1;
  foreach ($rooms as $id => $room)
  {
     $maxId = max($id, $maxId);
  }
  return $maxId;
}

/**
 * Create a new room
 *
 * @param string $name optional
 */
function mkRoom(string $name=NULL)
{
   global $rooms;
   $r = [];
   $r['id'] = getHighRoomId() + 1;
   $r['left'] = null;
   $r['right'] = null;
   $r['front'] = null;
   $r['back'] = null;
   $r['name'] = $name === null ? "room " . $r['id'] : $name;
   $r['things'] = [];
   $rooms[ $r['id'] ] = $r;
   return $r;
}

/**
 * Connect a (new) room to an existing other room
 *
 * @param int $baseId
 * @param int $otherId
 */
function connect($base, $direction, $dest)
{
  global $rooms;
  // todo: direction prüfen, obs die gibt
  $rooms[ $base['id'] ][ $direction ] = $dest[ 'id' ];
}

// Create the world:
$r = mkRoom();
$s = mkRoom();
connect($r, "right", $s);
connect($s, "left", $r);
print_r($rooms);

$guy = new tPlayer('t3o');

