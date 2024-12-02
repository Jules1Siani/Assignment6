import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ContactService {
  private apiUrl = 'http://localhost/contacts_backend/api/contacts.php';

  constructor(private http: HttpClient) {}

  // Récupérer tous les contacts
  getContacts(): Observable<any> {
    return this.http.get(this.apiUrl);
  }

  // Créer un contact avec une image facultative
  createContact(contact: any, imageFile?: File): Observable<any> {
    const formData = new FormData();
    formData.append('name', contact.name);
    formData.append('phone', contact.phone);

    // Ajouter le fichier image s'il existe
    if (imageFile) {
      formData.append('profile_picture', imageFile);
    }

    return this.http.post(this.apiUrl, formData);
  }

  // Update contact with an image on choice
  updateContact(contact: any, imageFile?: File): Observable<any> {
    const formData = new FormData();
    formData.append('id', contact.id.toString());
    formData.append('name', contact.name);
    formData.append('phone', contact.phone);

    // Add file if it's exist
    if (imageFile) {
      formData.append('profile_picture', imageFile);
    }

    return this.http.put(this.apiUrl, formData);
  }

  // Delete contact
  deleteContact(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}?id=${id}`);
  }
}
