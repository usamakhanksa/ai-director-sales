<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dso_maps_mock
 *
 * Deterministic mock of a real geocoding provider (Google Maps/Mapbox/
 * OpenCage). Nothing here calls a network endpoint - it maps a property's
 * city to an approximate real-world center point and nudges it by a small,
 * seeded offset so properties in the same city don't all land on one pin.
 * Cities not in the known list fall back to Riyadh's center point.
 *
 * Swap-out path: when a real geocoding provider is contracted, replace the
 * call to Dso_maps_mock::geocode() in Properties.php with a real HTTP call
 * (following the same dso_maps_mode 'live'/'mock'/'off' plumbing already used
 * by Dso_pms_mock/Dso_finance_mock). Keep the response shape identical.
 */
class Dso_maps_mock
{
    /** Approximate city-center coordinates for known Saudi cities. */
    protected $city_centers = array(
        'riyadh'    => array(24.7136, 46.6753),
        'jeddah'    => array(21.4858, 39.1925),
        'makkah'    => array(21.3891, 39.8579),
        'mecca'     => array(21.3891, 39.8579),
        'madinah'   => array(24.5247, 39.5692),
        'medina'    => array(24.5247, 39.5692),
        'dammam'    => array(26.4207, 50.0888),
        'khobar'    => array(26.2172, 50.1971),
        'taif'      => array(21.2703, 40.4158),
        'abha'      => array(18.2164, 42.5053),
        'tabuk'     => array(28.3998, 36.5715),
        'jubail'    => array(27.0046, 49.6611),
        'yanbu'     => array(24.0895, 38.0618),
        'najran'    => array(17.4924, 44.1277),
        'hail'      => array(27.5114, 41.7208),
    );

    /**
     * @param object $property row from dso_properties (uses id, city)
     * @return array{success:bool, lat:float, lng:float, synced_at:string, raw:array}
     */
    public function geocode($property)
    {
        $seed = (int) $property->id;
        $city_key = strtolower(trim((string) $property->city));
        $center = isset($this->city_centers[$city_key]) ? $this->city_centers[$city_key] : $this->city_centers['riyadh'];

        // Deterministic small offset (~ +/-0.05 degrees, a few km) seeded from the property id.
        $lat_offset = (($seed * 37) % 100 - 50) / 1000;
        $lng_offset = (($seed * 53) % 100 - 50) / 1000;

        $lat = round($center[0] + $lat_offset, 7);
        $lng = round($center[1] + $lng_offset, 7);

        return array(
            'success'   => true,
            'lat'       => $lat,
            'lng'       => $lng,
            'synced_at' => date('Y-m-d H:i:s'),
            'raw'       => array(
                'query'   => $property->city,
                'lat'     => $lat,
                'lng'     => $lng,
                'source'  => 'mock',
            ),
        );
    }
}
