import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { ToastController, LoadingController, IonicModule } from '@ionic/angular';
import { AuthService } from '../../core/auth.service';
import { OrdersService, WorkOrderListItem, WorkOrderStatus } from '../../core/orders.service';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-orders',
  templateUrl: './orders.page.html',
  styleUrls: ['./orders.page.scss'],
   imports: [IonicModule, CommonModule, FormsModule],
})
export class OrdersPage {
  orders: WorkOrderListItem[] = [];
  selectedStatus: WorkOrderStatus | 'ALL' = 'ALL';

  constructor(
    private ordersService: OrdersService,
    private auth: AuthService,
    private router: Router,
    private toast: ToastController,
    private loading: LoadingController
  ) {}

  ionViewWillEnter() {
    this.loadOrders();
  }

  async loadOrders() {
    const loader = await this.loading.create({ message: 'Cargando órdenes...' });
    await loader.present();

    const status = this.selectedStatus === 'ALL' ? undefined : this.selectedStatus;

    this.ordersService.list(status).subscribe({
      next: async (data) => {
        this.orders = data;
        await loader.dismiss();
      },
      error: async (err) => {
        await loader.dismiss();
        const msg = err?.status === 0 ? 'Sin conexión. Revisa tu internet.' : 'Error cargando órdenes';
        const t = await this.toast.create({ message: msg, duration: 2500 });
        await t.present();
      },
    });
  }

  goToDetail(id: number) {
    this.router.navigate(['/orders', id]);
  }

  async logout() {
    await this.auth.logout();
    await this.router.navigateByUrl('/login', { replaceUrl: true });
  }
}
