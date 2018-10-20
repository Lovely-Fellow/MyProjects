import { Injectable } from '@angular/core';
import { Observable} from 'rxjs';
import { of } from 'rxjs/observable/of';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { map } from 'rxjs/operator/map';

export interface Company{
  name: string;
  id: number;
}

@Injectable()
export class CompanylistService {
  //CompanyList: Company[];
  constructor( private http: HttpClient) { }
 
  getCompanyList (): Observable<Company[]> {
    return this.http.get<Company[]>(this.heroesUrl)
    .map(
      (res)=>res
    );
  }
  //private heroesUrl = 'app/CompanyList';  // URL to web api
  private heroesUrl = 'assets/data/companyList.json';
}
