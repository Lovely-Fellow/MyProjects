from django import forms

from .models import Subscriber


class SubscriberAddForm(forms.ModelForm):
    email = forms.EmailField(
        widget=forms.EmailInput(attrs={'class': 'form-control', 'placeholder': 'email'})
    )
    
    firstname = forms.CharField(
        widget=forms.TextInput(
            attrs={'class': 'form-control', 'placeholder': 'First Name'}
        )
    )

    lastname = forms.CharField(
        widget=forms.TextInput(
            attrs={'class': 'form-control', 'placeholder': 'Last Name'}
        )
    )
    
    class Meta:
        model = Subscriber
        fields = ('email', 'firstname', 'lastname')
       

