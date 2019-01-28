import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { OnboardingPartnerMerchantComponent } from './onboarding-partner-merchant.component';

describe('OnboardingPartnerMerchantComponent', () => {
  let component: OnboardingPartnerMerchantComponent;
  let fixture: ComponentFixture<OnboardingPartnerMerchantComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ OnboardingPartnerMerchantComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(OnboardingPartnerMerchantComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
