@extends('front.layouts.app')

@section('content')
  <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <h3 style="margin: 0; color: var(--theme-primary); text-transform: uppercase;">Call Helpline</h3>
    <a href="{{ url()->previous() }}" class="btn btn-link" style="color: #fff; text-decoration: none;">
      <span class="glyphicon glyphicon-circle-arrow-left"></span> Back
    </a>
  </div>

  <div class="front-card" style="padding: 24px; margin-bottom: 20px; text-align: center; border: 1px solid #eee; background: #fff; color: #000; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
      <div style="background: #f8f9fa; padding: 10px; border-radius: 8px; margin-right: 15px; border: 1px solid #eee;">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #000;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
      </div>
      <h4 style="margin: 0; color: #000; font-size: 1.5em; font-weight: 500;">24 X 7 Helpline Number</h4>
    </div>
    <p style="color: #666; margin-bottom: 20px; font-size: 1.1em;">Our helpline number is open for any of your queries.</p>
    <div style="background: #000; color: #fff; display: inline-block; padding: 12px 40px; border-radius: 8px; font-size: 1.4em; font-weight: 500;">
      {{ '+919046863875' }}
    </div>
  </div>

  <div class="front-card" style="padding: 24px; text-align: center; border: 1px solid #eee; background: #fff; color: #000; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
      <div style="margin-right: 15px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="currentColor" style="color: #25D366;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .018 5.394 0 12.03c0 2.115.552 4.18 1.597 6.011L0 24l6.117-1.604a11.782 11.782 0 005.926 1.593h.005c6.634 0 12.032-5.396 12.034-12.032.002-3.218-1.248-6.242-3.517-8.511z"></path></svg>
      </div>
      <h4 style="margin: 0; color: #000; font-size: 1.5em; font-weight: 500;">Whatsapp Number</h4>
    </div>
    <p style="color: #666; margin-bottom: 20px; font-size: 1.1em;">We available on whatsapp chat . Our agent will get back to asap.</p>
    <div style="background: #000; color: #fff; display: inline-block; padding: 12px 40px; border-radius: 8px; font-size: 1.4em; font-weight: 500;">
      {{ '+919046863875' }}
    </div>
  </div>
@endsection
