<?php
/**
 *
 */
namespace aae\geo {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\geo
	 */
	class Calculator {
	    /**
	     * Calculates geodetic distance between two Points specified by latitude/longitude using
	     * Vincenty inverse formula for ellipsoids
	     *
	     * @param  [type] $coord1 [description]
	     * @param  [type] $coord2 [description]
	     * @return [type]         [description]
	     */
		public static function distanceBetweenCoordinates($coord1, $coord2) {
			$lat1 = $coord1->lat;
			$lon1 = $coord1->lon;
			$lat2 = $coord2->lat;
			$lon2 = $coord2->lon;
			return self::distVincenty($lat1, $lon1, $lat2, $lon2);
		}

		public static function getFirstCoordinate($track) {
			foreach ($track as $element) {
				if ($element instanceof \aae\math\geospatial\Point) {
				    return $element;
				} else {
					return Calculator::getFirstCoordinate($element);
				}
			}
		}

	    /* Calculates geodetic distance between two points specified by latitude/longitude using
	     * Vincenty inverse formula for ellipsoids
	     *
	     *
	     * Vincenty Inverse Solution of Geodesics on the Ellipsoid
	     * Adapted from a version created by Chris Veness
	     *
	     * from: Vincenty inverse formula - T Vincenty,
	     * "Direct and Inverse Solutions of Geodesics on the Ellipsoid with
	     * application of nested equations", Survey Review, vol XXII no 176, 1975
	     *       http://www.ngs.noaa.gov/PUBS_LIB/inverse.pdf
	     *
	     * @param   float lat1, lon1: first point in decimal degrees
	     * @param   float lat2, lon2: second point in decimal degrees
	     * @returns float distance in km between points
	     */
	    public static function distVincenty($lat1, $lon1, $lat2, $lon2) {
	    	// WGS-84 ellipsoid params
	        $a = 6378137;
	        $b = 6356752.314245;
	        $f = 1/298.257223563;
	        $L = deg2rad(($lon2-$lon1));
	        $U1 = atan((1-$f) * tan(deg2rad($lat1)));
	        $U2 = atan((1-$f) * tan(deg2rad($lat2)));
	        $sinU1 = sin($U1);
	        $cosU1 = cos($U1);
	        $sinU2 = sin($U2);
	        $cosU2 = cos($U2);

	        $lambda = $L;
	        $iterLimit = 100;
	        do {
	            $sinLambda = sin($lambda);
	            $cosLambda = cos($lambda);
	            $sinSigma = sqrt(($cosU2*$sinLambda) * ($cosU2*$sinLambda) +
	                             ($cosU1*$sinU2-$sinU1*$cosU2*$cosLambda) *
	                             ($cosU1*$sinU2-$sinU1*$cosU2*$cosLambda));
	            if ($sinSigma==0) return 0;  // co-incident points
	            $cosSigma = $sinU1*$sinU2 + $cosU1*$cosU2*$cosLambda;
	            $sigma = atan2($sinSigma, $cosSigma);
	            $sinAlpha = $cosU1 * $cosU2 * $sinLambda / $sinSigma;
	            $cosSqAlpha = 1 - $sinAlpha*$sinAlpha;
	            $cos2SigmaM = $cosSigma - 2*$sinU1*$sinU2/$cosSqAlpha;

	            if ($cos2SigmaM == NAN) $cos2SigmaM = 0; // equatorial line: $cosSqAlpha=0 (§6)
	            $C = $f/16*$cosSqAlpha*(4+$f*(4-3*$cosSqAlpha));
	            $lambdaP = $lambda;
	            $lambda = $L + (1-$C) * $f * $sinAlpha *
	            ($sigma + $C*$sinSigma*($cos2SigmaM+$C*$cosSigma*(-1+2*$cos2SigmaM*$cos2SigmaM)));
	        } while (abs($lambda-$lambdaP) > 1e-12 && --$iterLimit>0);

	        if ($iterLimit==0) return NAN;  // formula failed to converge

	        $uSq = $cosSqAlpha * ($a*$a - $b*$b) / ($b*$b);
	        $A = 1 + $uSq/16384*(4096+$uSq*(-768+$uSq*(320-175*$uSq)));
	        $B = $uSq/1024 * (256+$uSq*(-128+$uSq*(74-47*$uSq)));
	        $deltaSigma = $B*$sinSigma*($cos2SigmaM+$B/4*($cosSigma*(-1+2*$cos2SigmaM*$cos2SigmaM) -
	        			  $B/6*$cos2SigmaM*(-3+4*$sinSigma*$sinSigma)*(-3+4*$cos2SigmaM*$cos2SigmaM)));
	        $s = $b*$A*($sigma-$deltaSigma);

	        //$s = round($s, 3); // round to 1mm precision
	        return $s/1000.0;

	        // note: to return initial/final bearings in addition to distance, use something like:
	        /*
	        $fwdAz = atan2($cosU2*$sinLambda,  $cosU1*$sinU2-$sinU1*$cosU2*$cosLambda);
	        $revAz = atan2($cosU1*$sinLambda, -$sinU1*$cosU2+$cosU1*$sinU2*$cosLambda);
	        return { $distance: $s, $initialBearing: deg2rad($fwdAz), $finalBearing: deg2rad($revAz) };*/
	    }

		/**
		 * [trackDistance description]
		 * @param  [type] $track [description]
		 * @return [type]        [description]
		 */
		public static function trackDistance($track) {
			$total = 0;
			$paused = 0;
			for ($i=1; $i < count($track->segmentStarts); $i++) {
				$distance = self::distanceBetweenCoordinates($track[$track->segmentStarts[$i]], $track[$track->segmentStarts[$i]-1]);
				if ($distance > 0) {
					$paused += $distance;
				}
			}
			for ($i=0; $i < count($track) - 1; $i++) {
				$distance = self::distanceBetweenCoordinates($track[$i], $track[$i+1]);
				if ($distance > 0) {
					$total += $distance;
				}
			}
			return $total - $paused;
		}

		/**
		 * [elevationChangeBetweenCoordinates description]
		 * @param  [type] $point1 [description]
		 * @param  [type] $point2 [description]
		 * @return [type]         [description]
		 */
		public static function elevationChangeBetweenCoordinates($point1, $point2) {
			$result = $point2->ele - $point1->ele;
			return $result;
		}

		/**
		 * [trackElevation description]
		 * @param  [type] $track [description]
		 * @return [type]        [description]
		 */
		public static function totalTrackElevation($track) {
			$result = array("elevGain" => 0, "elevLoss" => 0);
			for ($i=0; $i < count($track) - 1; $i++) {
				$elevation = self::elevationChangeBetweenCoordinates($track[$i], $track[$i+1]);
				if ($elevation > 0) {
					$result["elevGain"] += $elevation;
				} else {
					$result["elevLoss"] += $elevation;
				}
			}
			return $result;
		}

		/**
		 * Returns the amount of time passed from point1 to point2. Throws an exception when
		 * point2 has an earlier time than point1.
		 *
		 * @param Point __parameterDescription__
		 * @param Point __parameterDescription__
		 */
		public static function timePassedBetweenCoordinates($point1, $point2) {
			$result = $point2->time - $point1->time;
			if ($result < 0) {
				throw new \Exception("point2 has an earlier time than point1", 1);
			}
			return $result;
		}

		/**
		 * __functionDescription__
		 * @param __type__ __parameterDescription__
		 */
		public static function totalTrackTime($track) {
			$timePaused = 0;
			foreach ($track->segmentStarts as $segmentStart) {
				if ($segmentStart > 0) {
					$lastPoint = $track[$segmentStart];
					$firstPoint = $track[$segmentStart-1];
					$timePaused += self::timePassedBetweenCoordinates($firstPoint, $lastPoint);
				}
			}
			$lastPoint = $track[count($track)-1];
			$firstPoint = $track[0];
			$total = self::timePassedBetweenCoordinates($firstPoint, $lastPoint) - $timePaused;

			return $total;
		}

		/**
		 * Returns the average speed in km/h
		 * @param __type__ __parameterDescription__
		 */
		public static function averageTrackSpeed($track) {
			$distance = self::trackDistance($track);
			$time = self::totalTrackTime($track) / (60 * 60);
			$result = $distance / ($time);
			return $result;
		}
	}
}