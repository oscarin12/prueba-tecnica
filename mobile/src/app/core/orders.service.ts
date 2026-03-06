import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';

export type WorkOrderStatus = 'PENDIENTE' | 'EN_CURSO' | 'FINALIZADA';

export interface WorkOrderListItem {
  id: number;
  title: string;
  status: WorkOrderStatus;
  createdAt: string;
}

export interface WorkOrderDetail extends WorkOrderListItem {
  description: string | null;
}

@Injectable({ providedIn: 'root' })
export class OrdersService {
  constructor(private http: HttpClient) {}

  list(status?: WorkOrderStatus) {
    const url = status
      ? `${environment.apiUrl}/work-orders?status=${status}`
      : `${environment.apiUrl}/work-orders`;
    return this.http.get<WorkOrderListItem[]>(url);
  }

  detail(id: number) {
    return this.http.get<WorkOrderDetail>(`${environment.apiUrl}/work-orders/${id}`);
  }

  advanceStatus(id: number) {
    return this.http.patch<{ id: number; status: WorkOrderStatus }>(
      `${environment.apiUrl}/work-orders/${id}/status`,
      {}
    );
  }
}