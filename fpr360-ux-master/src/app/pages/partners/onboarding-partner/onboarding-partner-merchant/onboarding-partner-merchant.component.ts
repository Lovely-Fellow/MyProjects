import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { MatDialog} from '@angular/material';
import { OnboardingPartnerComponent} from '../onboarding-partner.component';
import { RadioListItemsComponent } from '../../../../dialogs/radio-list-items/radio-list-items.component';
import { Router} from '@angular/router'
import { CompanylistService, Company } from '../../companylist.service';


@Component({
  selector: 'app-onboarding-partner-merchant',
  templateUrl: './onboarding-partner-merchant.component.html',
  styleUrls: ['./onboarding-partner-merchant.component.scss']
})

export class OnboardingPartnerMerchantComponent implements OnInit {

  constructor(public dialog: MatDialog, private router: Router,
   private parent: OnboardingPartnerComponent, private companylistservice: CompanylistService
    ) {
    //including parent class or object
   
   }
   

  ngAfterContentInit(){
   // setTimeout(() => {
        this.openDialog();
     
    //});
  }


   getCompanyList(): void {
    this.companylistservice.getCompanyList()
    /*.pipe(
      map( res => res.map(company => {
        const companylist: Company = Object.assign({}, company, {active: false, included: false});
        return companylist;
      }))
    )*/
        .subscribe(companylist => this.companylist = companylist);
  }

  @Input() activePath:string;
  @Output() saveEvent = new EventEmitter<string>();
  ngOnInit() {
    this.getCompanyList();
   }
  
  companylist:Company[];
  staticcompanylist = [
    {
      "name": "First Merchant"
    },
    {
      "name": "Second Merchant"
    },
    {
      "name": "Third Merchant"
    }
  ]
  dataArray = [];
  @Input() dialogName: string;


  dlgName = "";
  openDialog(): void {
      //this.dataArray = this.companylist;
      this.dataArray = this.staticcompanylist;  
      if ( this.dialogName === undefined)
        this.dlgName = "MERCHANT";
      else
        this.dlgName = this.dialogName;
      const dialogRef = this.dialog.open(RadioListItemsComponent, {
        data: {
          dataArray: this.dataArray,
          dialogName:  this.dlgName
        },
        autoFocus: false
      });
      dialogRef.afterClosed().subscribe(result => {
        if (result)
        {
          this.parent.partnerInnerMenu.push({'path':"add", 'name':result, 'indicator':""});
          //this.saveEvent.emit(result);
        }
        
        this.router.navigate(["/onboarding-partner/" + "reports" /*this.activePath*/]);
   
      });
   
  }
     
}
  
  

