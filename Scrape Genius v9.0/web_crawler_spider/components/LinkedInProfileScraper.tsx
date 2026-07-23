'use client';

import React, { useState } from 'react';
import { useRouter } from 'next/navigation';
import { getToken } from '../lib/client-auth';

interface LinkedInProfileData {
  userProfile?: {
    fullName: string;
    firstName: string;
    lastName: string;
    title: string;
    location: string;
    photo: string;
    description: string;
    url: string;
  };
  experiences?: Array<{
    title: string;
    company: string;
    location: string;
    startDate: string;
    endDate: string;
    endDateIsPresent: boolean;
    description: string;
    durationInDays: number;
  }>;
  education?: Array<{
    schoolName: string;
    degreeName: string;
    fieldOfStudy: string;
    startDate: string;
    endDate: string;
    durationInDays: number;
  }>;
  volunteerExperiences?: Array<{
    title: string;
    company: string;
    description: string;
    startDate: string;
    endDate: string;
    endDateIsPresent: boolean;
  }>;
  skills?: Array<{
    skillName: string;
    endorsementCount: number;
  }>;
  contactInfo?: {
    email?: string;
    phone?: string;
  };
}

const LinkedInProfileScraper = () => {
  const [profileUrl, setProfileUrl] = useState('');
  const [sessionCookie, setSessionCookie] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [result, setResult] = useState<LinkedInProfileData | null>(null);
  const router = useRouter();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);
    setResult(null);
    setIsLoading(true);

    try {
      // Validate inputs
      if (!profileUrl || !sessionCookie) {
        throw new Error('Both profile URL and session cookie are required');
      }

      // Validate LinkedIn URL format
      const linkedinRegex = /^https:\/\/www\.linkedin\.com\/in\/[a-zA-Z0-9-_/]+\/?$/;
      if (!linkedinRegex.test(profileUrl)) {
        throw new Error('Invalid LinkedIn profile URL format');
      }

      const token = getToken();
      if (!token) {
        router.push('/login');
        return;
      }

      // Make API call to scrape LinkedIn profile
      const response = await fetch('/api/social/linkedin', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({
          profileUrl,
          sessionCookieValue: sessionCookie
        }),
      });

      const json = await response.json();

      if (!response.ok || !json.success) {
        throw new Error(json.error || 'Failed to scrape LinkedIn profile');
      }

      // Process the result
      setResult(json.data?.result ?? null);
    } catch (err: any) {
      console.error('LinkedIn scraping error:', err);
      setError(err.message || 'An error occurred while scraping the LinkedIn profile');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
      <h2 className="text-2xl font-bold mb-6 text-gray-800">LinkedIn Profile Scraper</h2>
      
      <form onSubmit={handleSubmit} className="space-y-6">
        <div>
          <label htmlFor="profileUrl" className="block text-sm font-medium text-gray-700 mb-1">
            LinkedIn Profile URL
          </label>
          <input
            type="url"
            id="profileUrl"
            value={profileUrl}
            onChange={(e) => setProfileUrl(e.target.value)}
            placeholder="https://www.linkedin.com/in/username"
            className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            required
          />
          <p className="mt-1 text-xs text-gray-500">
            Enter the full LinkedIn profile URL (e.g., https://www.linkedin.com/in/username)
          </p>
        </div>

        <div>
          <label htmlFor="sessionCookie" className="block text-sm font-medium text-gray-700 mb-1">
            Session Cookie Value
          </label>
          <input
            type="password"
            id="sessionCookie"
            value={sessionCookie}
            onChange={(e) => setSessionCookie(e.target.value)}
            placeholder="Enter your li_at cookie value"
            className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            required
          />
          <p className="mt-1 text-xs text-gray-500">
            Your LinkedIn session cookie (li_at) for authentication
          </p>
        </div>

        <button
          type="submit"
          disabled={isLoading}
          className={`w-full py-3 px-4 rounded-md text-white font-medium ${
            isLoading 
              ? 'bg-gray-400 cursor-not-allowed' 
              : 'bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500'
          }`}
        >
          {isLoading ? (
            <span className="flex items-center justify-center">
              <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Scraping...
            </span>
          ) : (
            'Scrape Profile'
          )}
        </button>
      </form>

      {error && (
        <div className="mt-6 p-4 bg-red-50 border border-red-200 rounded-md">
          <h3 className="text-red-800 font-medium">Error</h3>
          <p className="text-red-600">{error}</p>
        </div>
      )}

      {result && (
        <div className="mt-6">
          <h3 className="text-xl font-semibold mb-4 text-gray-800">Scraped Profile Data</h3>
          
          {result.userProfile && (
            <div className="mb-6 p-4 border border-gray-200 rounded-md">
              <h4 className="font-medium text-gray-700 mb-2">User Profile</h4>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-2">
                <p><span className="font-medium">Full Name:</span> {result.userProfile.fullName}</p>
                <p><span className="font-medium">Title:</span> {result.userProfile.title}</p>
                <p><span className="font-medium">Location:</span> {result.userProfile.location}</p>
                <p><span className="font-medium">URL:</span> <a href={result.userProfile.url} target="_blank" rel="noopener noreferrer" className="text-blue-600 hover:underline">{result.userProfile.url}</a></p>
              </div>
              {result.userProfile.description && (
                <div className="mt-2">
                  <p><span className="font-medium">Description:</span> {result.userProfile.description}</p>
                </div>
              )}
              {result.userProfile.photo && (
                <div className="mt-3">
                  <img src={result.userProfile.photo} alt="Profile" className="w-16 h-16 rounded-full object-cover border-2 border-gray-200" />
                </div>
              )}
            </div>
          )}

          {/* Display contact information if available */}
          {(result.contactInfo?.email || result.contactInfo?.phone) && (
            <div className="mb-6 p-4 border border-green-200 rounded-md bg-green-50">
              <h4 className="font-medium text-green-800 mb-2">Contact Information</h4>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-2">
                {result.contactInfo.email && (
                  <p><span className="font-medium">Email:</span> <a href={`mailto:${result.contactInfo.email}`} className="text-blue-600 hover:underline">{result.contactInfo.email}</a></p>
                )}
                {result.contactInfo.phone && (
                  <p><span className="font-medium">Phone:</span> <a href={`tel:${result.contactInfo.phone}`} className="text-blue-600 hover:underline">{result.contactInfo.phone}</a></p>
                )}
              </div>
              <div className="mt-2 text-sm text-green-700">
                <p><strong>Note:</strong> Contact information is only available if the profile owner has made it public.</p>
              </div>
            </div>
          )}

          {result.experiences && result.experiences.length > 0 && (
            <div className="mb-6">
              <h4 className="font-medium text-gray-700 mb-2">Experience</h4>
              <div className="space-y-3">
                {result.experiences.map((exp, index) => (
                  <div key={index} className="p-3 border border-gray-200 rounded-md">
                    <p><span className="font-medium">Position:</span> {exp.title}</p>
                    <p><span className="font-medium">Company:</span> {exp.company}</p>
                    <p><span className="font-medium">Duration:</span> {exp.startDate} - {exp.endDateIsPresent ? 'Present' : exp.endDate}</p>
                    {exp.description && <p><span className="font-medium">Description:</span> {exp.description}</p>}
                  </div>
                ))}
              </div>
            </div>
          )}

          {result.education && result.education.length > 0 && (
            <div className="mb-6">
              <h4 className="font-medium text-gray-700 mb-2">Education</h4>
              <div className="space-y-3">
                {result.education.map((edu, index) => (
                  <div key={index} className="p-3 border border-gray-200 rounded-md">
                    <p><span className="font-medium">School:</span> {edu.schoolName}</p>
                    <p><span className="font-medium">Degree:</span> {edu.degreeName}</p>
                    <p><span className="font-medium">Field:</span> {edu.fieldOfStudy}</p>
                    <p><span className="font-medium">Duration:</span> {edu.startDate} - {edu.endDate}</p>
                  </div>
                ))}
              </div>
            </div>
          )}

          {result.skills && result.skills.length > 0 && (
            <div>
              <h4 className="font-medium text-gray-700 mb-2">Skills</h4>
              <div className="flex flex-wrap gap-2">
                {result.skills.map((skill, index) => (
                  <span key={index} className="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                    {skill.skillName} ({skill.endorsementCount})
                  </span>
                ))}
              </div>
            </div>
          )}
        </div>
      )}
    </div>
  );
};

export default LinkedInProfileScraper;