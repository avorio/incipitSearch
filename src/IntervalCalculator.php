<?php

namespace ADWLM\IncipitSearch;

/**
 * IntervalCalculator transforms the normilized Incipit,
 * with accidentals mapped to each note into a notation with transposition that represents interval relations.
 *
 *
 * Copyright notice
 *
 * (c) 2016
 * Anna Neovesky  Anna.Neovesky@adwmainz.de
 * Frederic von Vlahovits Frederic.vonVlahovits@adwmainz.de
 *
 * Digital Academy www.digitale-akademie.de
 * Academy of Sciences and Literatur | Mainz www.adwmainz.de
 *
 * Licensed under The MIT License (MIT)
 *
 * @package ADWLM\IncipitSearch
 */
class IntervalCalculator
{

    // notes with value assigned to each semitone
    public static $notes = [
        'bC' => 11,
        'C' => 0,
        'xC' => 1,
        'bD' => 1,
        'D' => 2,
        'xD' => 3,
        'bE' => 3,
        'E' => 4,
        'xE' => 5,
        'bF' => 4,
        'F' => 5,
        'xF' => 6,
        'bG' => 6,
        'G' => 7,
        'xG' => 8,
        'bA' => 8,
        'A' => 9,
        'xA' => 10,
        'bB' => 10,
        'B' => 11,
        'xB' => 0,
    ];

    public static $pitchValues = array();


    /**
     * Creates an incipit with transposition (relative distance between two pitches)
     *
     * @param string incipit normalized to note values expanded accidentals(ABxCxF)
     *
     * @return string incipit with transposition
     */
    public static function transposeNormalizedNotes(string $incipit): string
    {
        if (empty($incipit)) {
            return '';
        }

        // default values
        $highOctaveValue = 0; // can be between 0-4
        $lowOctaveValue = 0; // can be between 0-4
        $accidentalValue = ""; // x or b
        $noteWasParsed = false;

        /**
         * go through string token by token and identify notes by checking each token, saving value, composing
         * full note out of the values and saving to array
         */
        foreach (str_split($incipit) as $token) {
            // this looks so ugly, because  switch does not allow regex; maybe rewrite as if /else
            switch ($token) {
                case ',':
                    $lowOctaveValue += 1;
                    break;
                case "'":
                    $highOctaveValue += 1;
                    break;
                case 'x':
                    $accidentalValue = 'x';
                    break;
                case 'b':
                    $accidentalValue = 'b';
                    break;
                case 'A':
                    IntervalCalculator::pushNotesToArray($token, $accidentalValue, $lowOctaveValue, $highOctaveValue);
                    $noteWasParsed = true;
                    break;
                case 'B':
                    IntervalCalculator::pushNotesToArray($token, $accidentalValue, $lowOctaveValue, $highOctaveValue);
                    $noteWasParsed = true;
                    break;
                case 'C':
                    IntervalCalculator::pushNotesToArray($token, $accidentalValue, $lowOctaveValue, $highOctaveValue);
                    $noteWasParsed = true;
                    break;
                case 'D':
                    IntervalCalculator::pushNotesToArray($token, $accidentalValue, $lowOctaveValue, $highOctaveValue);
                    $noteWasParsed = true;
                    break;
                case 'E':
                    IntervalCalculator::pushNotesToArray($token, $accidentalValue, $lowOctaveValue, $highOctaveValue);
                    $noteWasParsed = true;
                    break;
                case 'F':
                    IntervalCalculator::pushNotesToArray($token, $accidentalValue, $lowOctaveValue, $highOctaveValue);
                    $noteWasParsed = true;
                    break;
                case 'G':
                    IntervalCalculator::pushNotesToArray($token, $accidentalValue, $lowOctaveValue, $highOctaveValue);
                    $noteWasParsed = true;
                    break;
                case 'B':
                    IntervalCalculator::pushNotesToArray($token, $accidentalValue, $lowOctaveValue, $highOctaveValue);
                    $noteWasParsed = true;
                    break;
                default:
                    // echo 'Invalid Incipit';
                    break;
            }
            if ($noteWasParsed) {
                $accidentalValue = '';
                $lowOctaveValue = 0;
                $highOctaveValue = 0;
                $noteWasParsed = false;
            }
        }

        return IntervalCalculator::calculateIntervals();
    }

    /**
     * Calculates numeric value of each note that represents its pitch
     *
     * @param $lowOctaveValue  value of low octave
     * @param $highOctaveValue value of high octave
     * @param $noteValue       value of note
     *
     */
    public static function calculatePitch($lowOctaveValue, $highOctaveValue, $noteString): int
    {
        // assign numeric value to each note
        $noteValue = IntervalCalculator::$notes[$noteString];
        if ($lowOctaveValue) {
            return (-12 * $lowOctaveValue) + $noteValue;
            // > 1, because '-Octave is just the note value
        } elseif ($highOctaveValue > 1) {
            return (12 * $highOctaveValue) + $noteValue;
        }

        return $noteValue;
    }


    /**
     * Sets values and pushes them to notes array
     *
     * @param $token
     * @param $accidentalValue
     * @param $lowOctaveValue
     * @param $highOctaveValue
     */
    public static function pushNotesToArray($token, $accidentalValue, $lowOctaveValue, $highOctaveValue)
    {
        $noteString = $accidentalValue . $token;
        array_push(IntervalCalculator::$pitchValues, IntervalCalculator::
        calculatePitch($lowOctaveValue, $highOctaveValue, $noteString));
    }

    /**
     *  Calculates difference between two pitches
     *
     * @param $pitchValues Array containing all pitch values
     *
     * @return string
     */
    public static function calculateIntervals(): string
    {
        $calculatedIntervals = '';
        $currentPitch = current(IntervalCalculator::$pitchValues);
        // go though pitches and suvtect nect from current
        while (next(IntervalCalculator::$pitchValues) !== false) {
            $nextPitch = current(IntervalCalculator::$pitchValues);
            $intervalValue = $nextPitch - $currentPitch;
            $interval = (string)$intervalValue;
            $calculatedIntervals = $calculatedIntervals . ' ' . $interval;
            $currentPitch = current(IntervalCalculator::$pitchValues);
        }

        //echo "ERGEBNIS: " . $calculatedIntervals . "\n";
        return $calculatedIntervals;
    }
}
