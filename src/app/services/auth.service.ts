import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable, tap } from 'rxjs';
import { Usuario, LoginResponse, RegisterResponse } from '../core/models/user.model';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  // URL base del backend PHP (XAMPP por defecto en puerto 80)
  private apiUrl = 'http://localhost/Proyecto_Angular/backend/api/auth';

  private readonly STORAGE_KEY = 'aeromuseo_user';

  // ─── Estado reactivo del usuario ─────────────────────────────────────────
  // Emite el usuario actual (o null si no hay sesión) a cualquier suscriptor.
  // El navbar y otros componentes pueden usarlo para reaccionar en tiempo real.
  private _usuario$ = new BehaviorSubject<Usuario | null>(this.getUsuarioActual());
  readonly usuario$ = this._usuario$.asObservable();

  constructor(private http: HttpClient) { }

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
    return this.http.post<RegisterResponse>(`${this.apiUrl}/register.php`, { nombre, email, password });
  }

  // ─── Verificación de Email ────────────────────────────────────────────────

  verifyEmail(token: string): Observable<{ success: boolean; message: string; nombre?: string }> {
    return this.http.get<{ success: boolean; message: string; nombre?: string }>(`${this.apiUrl}/verify.php?token=${token}`);
  }

  // ─── Recuperación de Contraseña ───────────────────────────────────────────

  forgotPassword(email: string): Observable<{ success: boolean; message: string }> {
    return this.http.post<{ success: boolean; message: string }>(`${this.apiUrl}/forgot-password.php`, { email });
  }

  resetPassword(token: string, password: string): Observable<{ success: boolean; message: string }> {
    return this.http.post<{ success: boolean; message: string }>(`${this.apiUrl}/reset-password.php`, { token, password });
  }

  // ─── Sesión ───────────────────────────────────────────────────────────────

  private guardarSesion(user: Usuario): void {
    localStorage.setItem(this.STORAGE_KEY, JSON.stringify(user));
    this._usuario$.next(user); // notifica a todos los suscriptores
  }

  logout(): void {
    localStorage.removeItem(this.STORAGE_KEY);
    this._usuario$.next(null); // notifica que ya no hay sesión
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

  // Compara el campo `rol` del modelo — no depende de `isAdmin`
  esAdmin(): boolean {
    return this.getUsuarioActual()?.rol === 'admin';
  }
}
