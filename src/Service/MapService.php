<?php

namespace App\Service;

class MapService
{
    public function generateMapHTML(float $latitude, float $longitude, string $emplacement, string $characteristics): string
    {
        $mapHTML = <<<HTML
        <div id="map" style="height: 400px;"></div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const map = L.map('map').setView([{$latitude}, {$longitude}], 15);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(map);
                
                L.marker([{$latitude}, {$longitude}], {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34]
                    })
                }).addTo(map)
                .bindPopup("<b>{$this->escapeJs($emplacement)}</b><br>{$this->escapeJs($characteristics)}");
            });
        </script>
        HTML;

        return $mapHTML;
    }

    private function escapeJs(string $input): string
    {
        return addslashes(htmlspecialchars($input, ENT_QUOTES));
    }
}