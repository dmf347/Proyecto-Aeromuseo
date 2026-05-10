import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';

export interface Usuario {
  id: number;
  nombre: string;
  email: string;
  rol: 'admin' | 'visitante';
  isAdmin: boolean;
}

export interface LoginResponse {
  success: boolean;
  message: string;
  user?: Usuario;
}

export interface RegisterResponse {
  success: boolean;
  message: string;
  user?: Usuario;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  // URL base del backend PHP (XAMPP por defecto en puerto 80)
  private apiUrl = 'http://localhost/Proyecto_Angular/backend/api/auth';

  private readonly STORAGE_KEY = 'aeromuseo_user';

  constructor(private http: HttpClient) {}

  // ─── Login ────────────────────────────────────────────────────────────────

  login(email: string, password: string): Observable<LoginResponse> {
    return this.http
      .post<LoginResponse>(`${this.apiUrl}/login.php`, { email, password })
      .pipe(
        tap(response => {
          if (response.success && response.user) {
            this.guardarSesion(response.user);
          }
        })
      );
  }

  // ─── Registro ─────────────────────────────────────────────────────────────

  register(nombre: string, email: string, password: string): Observable<RegisterResponse> {
    return this.http
      .post<RegisterResponse>(`${this.apiUrl}/register.php`, { nombre, email, password })
      .pipe(
        tap(response => {
          if (response.success && response.user) {
            this.guardarSesion(response.user);
          }
        })
      );
  }

  // ─── Sesión ───────────────────────────────────────────────────────────────

  private guardarSesion(user: Usuario): void {
    localStorage.setItem(this.STORAGE_KEY, JSON.stringify(user));
  }

  logout(): void {
    localStorage.removeItem(this.STORAGE_KEY);
  }

  getUsuarioActual(): Usuario | null {
    const data = localStorage.getItem(this.STORAGE_KEY);
    if (!data) return null;
    try {
      return JSON.parse(data) as Usuario;
    } catch {
      return null;
    }
  }

  estaLogueado(): boolean {
    return this.getUsuarioActual() !== null;
  }

  esAdmin(): boolean {
    return this.getUsuarioActual()?.isAdmin === true;
  }
}
