import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface ReservaRequest {
  usuario_id: number;
  fecha_visita: string;
  num_personas: number;
  comentarios: string;
}

export interface ReservaResponse {
  success: boolean;
  message: string;
}

@Injectable({
  providedIn: 'root'
})
export class ReservasService {
  private apiUrl = 'http://localhost/Proyecto_Angular/backend/api/reservas';

  constructor(private http: HttpClient) {}

  crearReserva(reserva: ReservaRequest): Observable<ReservaResponse> {
    return this.http.post<ReservaResponse>(`${this.apiUrl}/crear.php`, reserva);
  }
}
