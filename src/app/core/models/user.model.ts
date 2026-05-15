/**
 * Modelo de usuario autenticado.
 * Refleja la respuesta del backend PHP tras un login/registro exitoso.
 */
export interface Usuario {
  id: number;
  nombre: string;
  email: string;
  rol: 'admin' | 'visitante';
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
