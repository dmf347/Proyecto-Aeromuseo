import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-museo',
  imports: [RouterLink, CommonModule],
  templateUrl: './museo.html',
  styleUrl: './museo.css',
})
export class Museo {
  constructor(public authService: AuthService) {}
}
