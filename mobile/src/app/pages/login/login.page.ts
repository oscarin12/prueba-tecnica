import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { ToastController, LoadingController, IonicModule } from '@ionic/angular';
import { AuthService } from '../../core/auth.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
    imports: [IonicModule, CommonModule, FormsModule],
})
export class LoginPage {
  email = 'tech1@ott.cl';
  password = 'Tech123!';
  isSubmitting = false;

  constructor(
    private auth: AuthService,
    private router: Router,
    private toast: ToastController,
    private loading: LoadingController
  ) {}

  async onSubmit() {
  const loader = await this.loading.create({ message: 'Ingresando...' });
  await loader.present();

  try {
    const res = await this.auth.login(this.email, this.password);
    await this.auth.setToken(res.token);
    await this.router.navigateByUrl('/orders', { replaceUrl: true });
  } catch (err: any) {
    let msg = 'Error al iniciar sesión';
    if (err?.status === 0) msg = 'Sin conexión. Revisa tu internet.';
    if (err?.status === 401) msg = 'Credenciales inválidas';
    const t = await this.toast.create({ message: msg, duration: 2500 });
    await t.present();
  } finally {
    await loader.dismiss();
  }
}
}
