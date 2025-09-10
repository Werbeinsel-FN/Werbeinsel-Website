"use client";
import { useEffect, useRef } from "react";
import type { Map as LeafletMapType } from "leaflet";

type LeafletModule = typeof import("leaflet");

export default function LeafletMap() {
  const mapRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    let map: LeafletMapType | null = null;
    if (!mapRef.current) return;

    import("leaflet").then((Lmod: LeafletModule) => {
      const L = Lmod;
      map = L.map(mapRef.current!).setView([47.6543, 9.4797], 10);

      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution:
          'Leaflet | © <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
        maxZoom: 18,
      }).addTo(map);
    });

    return () => {
      if (map) map.remove();
    };
  }, []);

  return (
    <div
      ref={mapRef}
      className="w-full h-96 rounded-3xl overflow-hidden relative z-0"
    />
  );
}
