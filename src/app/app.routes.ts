import { Routes } from '@angular/router';
import { Inicio } from './features/inicio/inicio';
import { Login } from './features/login/login';
import { Museo } from './features/museo/museo';
import { Eventos } from './features/eventos/eventos';
import { VerifyEmail } from './features/verify-email/verify-email';
import { ResetPassword } from './features/reset-password/reset-password';
import { Reservas } from './features/reservas/reservas';
import { Admin } from './features/admin/admin';
import { authGuard } from './guards/auth.guard';
import { adminGuard } from './guards/admin.guard';

export const routes: Routes = [
  { path: '', component: Inicio },
  { path: 'login', component: Login },
  { path: 'verificar-email', component: VerifyEmail },
  { path: 'reset-password', component: ResetPassword },
  { path: 'museo', component: Museo },
  { path: 'eventos', component: Eventos },

  // Ruta protegida: solo usuarios logueados
  { path: 'reservas', component: Reservas, canActivate: [authGuard] },

  // Ruta protegida: solo administradores
  { path: 'admin', component: Admin, canActivate: [adminGuard] },

  { path: '**', redirectTo: '' }
];
