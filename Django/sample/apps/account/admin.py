from django.contrib import admin

from .models import Subscriber


class SubscriberAdmin(admin.ModelAdmin):
    list_display = ('email', 'firstname', 'lastname', 'created_at')
    list_filter = ('created_at',)


admin.site.register(Subscriber, SubscriberAdmin)
