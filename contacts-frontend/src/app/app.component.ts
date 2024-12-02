import { Component, OnInit } from '@angular/core';
import { ContactService } from './services/contact.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
  contacts: any[] = [];
  newContact = { name: '', phone: '' };
  editContact: any = null;
  selectedFile: File | null = null;

  constructor(private contactService: ContactService) {}

  ngOnInit(): void {
    this.loadContacts();
  }

  loadContacts(): void {
    this.contactService.getContacts().subscribe((data) => {
      this.contacts = data;
    });
  }

  onFileSelected(event: any): void {
    this.selectedFile = event.target.files[0];
  }

  addContact(): void {
    const fileToSend = this.selectedFile || undefined; // Gérer null en le remplaçant par undefined
    this.contactService.createContact(this.newContact, fileToSend).subscribe(() => {
      this.loadContacts();
      this.newContact = { name: '', phone: '' };
      this.selectedFile = null;
    });
  }

  enableEdit(contact: any): void {
    this.editContact = { ...contact };
  }

  updateContact(): void {
    if (this.editContact) {
      const fileToSend = this.selectedFile || undefined; // Gérer null en le remplaçant par undefined
      this.contactService.updateContact(this.editContact, fileToSend).subscribe(() => {
        this.loadContacts();
        this.editContact = null;
        this.selectedFile = null;
      });
    }
  }

  deleteContact(id: number): void {
    this.contactService.deleteContact(id).subscribe(() => {
      this.loadContacts();
    });
  }
}
