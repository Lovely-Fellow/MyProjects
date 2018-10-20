import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { PartnersComponent } from './partners.component';
import { PartnerComponent } from './partner/partner.component';
import { PartnerDetaisComponent } from './partner/partner-detais/partner-detais.component';
import { PartnerClientsComponent } from './partner/partner-clients/partner-clients.component';
import { PartnerReportsComponent } from './partner/partner-reports/partner-reports.component';
import { OnboardingPartnerComponent } from './onboarding-partner/onboarding-partner.component';
import { OnboardingPartnerDetailsComponent } from './onboarding-partner/onboarding-partner-details/onboarding-partner-details.component';
import { OnboardingPartnerClientsComponent } from './onboarding-partner/onboarding-partner-clients/onboarding-partner-clients.component';
import { OnboardingPartnerReportsComponent } from './onboarding-partner/onboarding-partner-reports/onboarding-partner-reports.component';
import { OnboardingPartnerMerchantComponent } from './onboarding-partner/onboarding-partner-merchant/onboarding-partner-merchant.component';
import { PartnerActivityComponent } from './partner/partner-activity/partner-activity.component';

const innerPartner: Routes = [
    { path: 'details', component: PartnerDetaisComponent },
    { path: 'clients', component: PartnerClientsComponent },
    { path: 'reports', component: PartnerReportsComponent },
    { path: 'activity', component: PartnerActivityComponent }
  ];
  
  const innerOnboardingPartner: Routes = [
    { path: 'details', component: OnboardingPartnerDetailsComponent },
    { path: 'clients', component: OnboardingPartnerClientsComponent },
    { path: 'reports', component: OnboardingPartnerReportsComponent },
    { path: 'merchant', component: OnboardingPartnerMerchantComponent }
  ];
  const partnerRoutes: Routes = [
    { path: 'partners', component: PartnersComponent },
    { path: 'partner', component: PartnerComponent, children: innerPartner },
    { path: 'onboarding-partner', component: OnboardingPartnerComponent, children: innerOnboardingPartner },
  ]

  @NgModule({
    imports: [ RouterModule.forChild(partnerRoutes) ],
    exports: [ RouterModule ]
  })

  export class PartnersRouteModule{
    static components = [ 
      PartnersComponent,
      PartnerComponent, 
      PartnerDetaisComponent, 
      PartnerClientsComponent, 
      PartnerReportsComponent,
      OnboardingPartnerComponent, 
      OnboardingPartnerDetailsComponent,
      OnboardingPartnerClientsComponent,
      OnboardingPartnerReportsComponent, 
      OnboardingPartnerMerchantComponent,
      PartnerActivityComponent ];
}