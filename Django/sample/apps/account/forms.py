from django import forms

from .models import Subscriber


class SubscriberAddForm(forms.ModelForm):
    email = forms.EmailField(
        widget=forms.EmailInput(attrs={'class': 'form-control', 'placeholder': 'email'})
    )

    class Meta:
        model = Subscriber
        fields = ('email', )

