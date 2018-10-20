import { Component, OnInit } from '@angular/core';
import { MessageService } from '../message.service';
@Component({
  selector: 'app-messages-component',
  templateUrl: './messages-component.component.html',
  styleUrls: ['./messages-component.component.css']
})
export class MessagesComponentComponent implements OnInit {

  constructor(public messageService: MessageService) { }

  ngOnInit() {
  }

}
