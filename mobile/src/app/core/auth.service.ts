import { Injectable } from '@angular/core';
import { Preferences } from '@capacitor/preferences';
import { CapacitorHttp } from '@capacitor/core';
import { environment } from '../../environments/environment';

const TOKEN_KEY = 'auth_token';

@Injectable({ providedIn: 'root' })
export class AuthService {
  async getToken(): Promise<string | null> {
    const { value } = await Preferences.get({ key: TOKEN_KEY });
    return value ?? null;
  }

  async setToken(token: string): Promise<void> {
    await Preferences.set({ key: TOKEN_KEY, value: token });
  }

  async logout(): Promise<void> {
    await Preferences.remove({ key: TOKEN_KEY });
  }

  async login(email: string, password: string): Promise<{ token: string }> {
    const res = await CapacitorHttp.post({
      url: `${environment.apiUrl}/login`,
      headers: { 'Content-Type': 'application/json' },
      data: { email, password },
    });

    // res.data trae el JSON
    return res.data as { token: string };
  }
}