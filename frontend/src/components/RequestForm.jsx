import React, { useState } from 'react';
import { BeakerIcon, PaperAirplaneIcon, ShieldCheckIcon } from '@heroicons/react/24/outline';

const RequestForm = () => {
  const [formData, setFormData] = useState({
    requester_id: 1, // Mocked logged-in user ID
    drug_id: '',
    quantity: '',
    urgency_level: 'Low',
  });

  const [statusMessage, setStatusMessage] = useState({ type: '', text: '' });
  const [isSubmitting, setIsSubmitting] = useState(false);

  // Mock drug list for dropdown
  const drugsList = [
    { id: 1, name: 'Paracetamol 500mg' },
    { id: 2, name: 'Amoxicillin 250mg' },
    { id: 3, name: 'Adrenaline 1mg/ml' },
    { id: 4, name: 'Normal Saline 1000ml' },
  ];

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    setStatusMessage({ type: '', text: '' });

    try {
      // In a real app, you might use an environment variable for the base URL
      const response = await fetch('http://localhost/pharmalink/api/create_request.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData),
      });

      const data = await response.json();

      if (response.ok && data.success) {
        setStatusMessage({ type: 'success', text: `Success! Ticket #${data.ticket_id} created.` });
        setFormData(prev => ({ ...prev, drug_id: '', quantity: '', urgency_level: 'Low' }));
      } else {
        setStatusMessage({ type: 'error', text: data.message || 'Something went wrong.' });
      }
    } catch (error) {
      console.error('Submission error:', error);
      setStatusMessage({ type: 'error', text: 'Network error. Could not connect to API.' });
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="min-h-screen bg-slate-50 flex items-center justify-center p-6 font-sans">
      <div className="w-full max-w-xl bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
        
        {/* Header Section */}
        <div className="bg-gradient-to-r from-blue-600 to-indigo-700 p-8 text-white relative overflow-hidden">
          <div className="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
          <div className="relative z-10 flex items-center gap-4">
            <div className="p-3 bg-white/20 rounded-2xl backdrop-blur-sm border border-white/10 shadow-inner">
              <BeakerIcon className="w-8 h-8 text-white" />
            </div>
            <div>
              <h2 className="text-2xl font-bold tracking-tight">New Medication Request</h2>
              <p className="text-blue-100 mt-1 text-sm font-medium">Initiate an inter-hospital transfer</p>
            </div>
          </div>
        </div>

        {/* Form Section */}
        <div className="p-8">
          {statusMessage.text && (
            <div className={`mb-6 p-4 rounded-xl flex items-center gap-3 text-sm font-medium transition-all ${
              statusMessage.type === 'success' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200'
            }`}>
              {statusMessage.type === 'success' ? <ShieldCheckIcon className="w-5 h-5 text-emerald-600" /> : <ShieldCheckIcon className="w-5 h-5 text-red-600" />}
              {statusMessage.text}
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="space-y-1.5">
              <label htmlFor="drug_id" className="block text-sm font-semibold text-slate-700">Select Medication</label>
              <select
                id="drug_id"
                name="drug_id"
                value={formData.drug_id}
                onChange={handleChange}
                required
                className="w-full px-4 py-3 bg-slate-50 border border-slate-200 text-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow outline-none appearance-none"
              >
                <option value="" disabled>-- Choose a drug --</option>
                {drugsList.map(drug => (
                  <option key={drug.id} value={drug.id}>{drug.name}</option>
                ))}
              </select>
            </div>

            <div className="grid grid-cols-2 gap-6">
              <div className="space-y-1.5">
                <label htmlFor="quantity" className="block text-sm font-semibold text-slate-700">Quantity</label>
                <input
                  type="number"
                  id="quantity"
                  name="quantity"
                  value={formData.quantity}
                  onChange={handleChange}
                  min="1"
                  required
                  placeholder="e.g. 50"
                  className="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow outline-none"
                />
              </div>

              <div className="space-y-1.5">
                <label htmlFor="urgency_level" className="block text-sm font-semibold text-slate-700">Urgency Level</label>
                <select
                  id="urgency_level"
                  name="urgency_level"
                  value={formData.urgency_level}
                  onChange={handleChange}
                  className="w-full px-4 py-3 bg-slate-50 border border-slate-200 text-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow outline-none appearance-none"
                >
                  <option value="Low">Low (Routine)</option>
                  <option value="Medium">Medium</option>
                  <option value="High">High</option>
                  <option value="Emergency">Emergency</option>
                </select>
              </div>
            </div>

            <div className="pt-4 border-t border-slate-100">
              <button
                type="submit"
                disabled={isSubmitting}
                className="w-full flex justify-center items-center gap-2 px-6 py-3.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all active:scale-[0.98] disabled:opacity-70 shadow-md shadow-indigo-600/20"
              >
                {isSubmitting ? (
                  <span className="flex items-center gap-2">
                    <svg className="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                      <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                  </span>
                ) : (
                  <>
                    Submit Request
                    <PaperAirplaneIcon className="w-5 h-5" />
                  </>
                )}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
};

export default RequestForm;
