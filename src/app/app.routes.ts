import { Routes } from '@angular/router';
import { Inicio } from './inicio/inicio';
import { Login } from './login/login';
import { Museo } from './museo/museo';
import { Eventos } from './eventos/eventos';
import { authGuard } from './guards/auth.guard';
import { adminGuard } from './guards/admin.guard';

export const routes: Routes = [
  { path: '',        component: Inicio },
  { path: 'login',   component: Login },
  { path: 'museo',   component: Museo },
  { path: 'eventos', component: Eventos },

  // Ruta protegida: solo usuarios logueados
  // { path: 'reservas', component: Reservas, canActivate: [authGuard] },

  // Ruta protegida: solo administradores
  // { path: 'admin',    component: Admin,    canActivate: [adminGuard] },

  { path: '**', redirectTo: '' }
];
