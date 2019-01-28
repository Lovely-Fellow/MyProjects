import { Component, OnInit, Input } from '@angular/core';

@Component({
  selector: 'app-sub-header',
  templateUrl: './sub-header.component.html',
  styleUrls: ['./sub-header.component.scss']
})
export class SubHeaderComponent implements OnInit {
  @Input() innerMenuItems: Array<string>;
  @Input() light: boolean = false;

  constructor() {}

  ngOnInit() {}
}
