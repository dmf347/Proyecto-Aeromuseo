import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-eventos',
  imports: [RouterLink, CommonModule],
  templateUrl: './eventos.html',
  styleUrl: './eventos.css',
})
export class Eventos {
  constructor(public authService: AuthService) {}
}
