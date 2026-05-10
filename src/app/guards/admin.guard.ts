import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

/**
 * Guard para rutas exclusivas de administradores.
 * Si el usuario no es admin, redirige a inicio.
 */
export const adminGuard: CanActivateFn = () => {
  const auth   = inject(AuthService);
  const router = inject(Router);

  if (auth.estaLogueado() && auth.esAdmin()) {
    return true;
  }

  if (!auth.estaLogueado()) {
    router.navigate(['/login']);
  } else {
    // Está logueado pero no es admin
    router.navigate(['/']);
  }

  return false;
};
