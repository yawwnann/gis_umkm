import 'leaflet';

declare module 'leaflet' {
  interface Layer {
    _map?: Map | undefined;
  }
}
