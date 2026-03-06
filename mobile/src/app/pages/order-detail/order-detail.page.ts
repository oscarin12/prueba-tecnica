import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastController, LoadingController, IonicModule } from '@ionic/angular';
import { OrdersService, WorkOrderDetail } from '../../core/orders.service';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-order-detail',
  templateUrl: './order-detail.page.html',
  styleUrls: ['./order-detail.page.scss'],
  imports: [IonicModule, CommonModule, FormsModule],
})
export class OrderDetailPage {
  order: WorkOrderDetail | null = null;
  id!: number;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private orders: OrdersService,
    private toast: ToastController,
    private loading: LoadingController
  ) {}

  ionViewWillEnter() {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.loadDetail();
  }

  async loadDetail() {
    const loader = await this.loading.create({ message: 'Cargando detalle...' });
    await loader.present();

    this.orders.detail(this.id).subscribe({
      next: async (data) => {
        this.order = data;
        await loader.dismiss();
      },
      error: async (err) => {
        await loader.dismiss();
        const msg = err?.status === 0 ? 'Sin conexión. Revisa tu internet.' : 'No se pudo cargar el detalle';
        const t = await this.toast.create({ message: msg, duration: 2500 });
        await t.present();
        this.router.navigateByUrl('/orders', { replaceUrl: true });
      },
    });
  }

  async advanceStatus() {
    if (!this.order) return;

    const loader = await this.loading.create({ message: 'Actualizando estado...' });
    await loader.present();

    this.orders.advanceStatus(this.id).subscribe({
      next: async (res) => {
        await loader.dismiss();
        // refrescar estado local
        this.order = { ...this.order!, status: res.status };
        const t = await this.toast.create({ message: `Estado actualizado: ${res.status}`, duration: 2000 });
        await t.present();
      },
      error: async (err) => {
        await loader.dismiss();
        const msg = err?.status === 0 ? 'Sin conexión. Revisa tu internet.' : 'No se pudo actualizar estado';
        const t = await this.toast.create({ message: msg, duration: 2500 });
        await t.present();
      },
    });
  }
}