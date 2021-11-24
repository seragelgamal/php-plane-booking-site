<?php

function bubbleSort(array &$anArray) {
  // decrement number of comparisons: start at array length - 1, go until 1
  for ($numberOfComparisons = sizeof($anArray) - 1; $numberOfComparisons > 0; $numberOfComparisons--) {
    // pass through the area the right number of times and swap the values if they're out of order
    for ($i = 0; $i < $numberOfComparisons; $i++) {
      if ($anArray[$i] > $anArray[$i + 1]) {
        $element = $anArray[$i];
        $anArray[$i] = $anArray[$i + 1];
        $anArray[$i + 1] = $element;
      }
    }
  }
}

function selectionSort(array &$anArray) {
  // cycle through all array elements as the fill slot except for the last one
  for ($fillSlot = 0; $fillSlot < sizeof($anArray) - 1; $fillSlot++) {
    // initially set the index of the minimum unsorted value to the index of the fill slot
    $indexOfMinimum = $fillSlot;
    // loop through all the values of the array after the fill slot and set the index of the minimum unsorted value to the smallest one
    for ($i = $fillSlot + 1; $i < sizeof($anArray); $i++) {
      if ($anArray[$i] < $anArray[$indexOfMinimum]) {
        $indexOfMinimum = $i;
      }
    }
    // swap the fill slot and minimum unsorted element
    $element = $anArray[$fillSlot];
    $anArray[$fillSlot] = $anArray[$indexOfMinimum];
    $anArray[$indexOfMinimum] = $element;
  }
}

function insertionSort(array &$anArray) {
  // cycle through all elements from the second one to the last one
  for ($i = 1; $i < sizeof($anArray); $i++) {
    // set the insert position initially to the index of the current element
    $insertPosition = $i;
    // save the element at index $i, because it could be overwritten
    $valueToInsert = $anArray[$i];
    // check if the value on the left side is greater than the current element being sorted 
    while ($insertPosition != 0 && $anArray[$insertPosition - 1] > $valueToInsert) {
      // move the value that's on the left to the current insert position
      $anArray[$insertPosition] = $anArray[$insertPosition - 1];
      // move the insert position to the left
      $insertPosition--;
    }
    // now that the insert position is in the right spot, insert the value
    $anArray[$insertPosition] = $valueToInsert;
  }
}